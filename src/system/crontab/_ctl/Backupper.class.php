<?php
/**
 * 自动备份脚本，备份MySQL数据库，通过SSH+Rsync备份文件目录
 *
 * @author john
 */
namespace Lge;

if (!defined('LGE')) {
	exit('Include Permission Denied!');
}

/**
 * Class Controller_Backupper
 * @package Lge
 */
class Controller_Backupper extends Controller_Base
{
    public $logCategory = 'crontab/backupper';
    
    /**
     * 默认入口函数.
     *
     * @return void
     */
    public function index()
    {
        Logger::log('==================start===================', $this->logCategory);

        $config       = Config::getFile('backupper', true);
        $clientConfig = $config['backup_client'];
        $serverConfig = $config['backup_server'];
        $backupDir    = rtrim($clientConfig['folder'], '/');
        foreach ($serverConfig as $groupName => $backupConfig) {
            // 首先备份数据库
            if (!empty($backupConfig['data'])) {
                foreach ($backupConfig['data'] as $dataConfig) {
                    $host = $dataConfig['host'];
                    $port = $dataConfig['port'];
                    $user = $dataConfig['user'];
                    $pass = $dataConfig['pass'];
                    $dataBackupDir = "{$backupDir}/{$groupName}/{$host}/data";
                    if (!file_exists($dataBackupDir)) {
                        @mkdir($dataBackupDir, 0777, true);
                    }
                    // 执行备份
                    foreach ($dataConfig['names'] as $name => $keepDays) {
                        $date          = date('Ymd');
                        $filePath      = "{$dataBackupDir}/{$name}.{$date}.sql.bz2";
                        $filePathTemp  = "{$dataBackupDir}/{$name}.{$date}.temp.sql.bz2";
                        $localShellCmd = "mysqldump -C -h{$host} -P{$port} -u{$user} -p{$pass} {$name} | bzip2 > {$filePathTemp}";
                        Logger::log("Backing up database: {$filePath}", $this->logCategory);

                        try {
                            $result = @shell_exec($localShellCmd);
                            Logger::log($result, $this->logCategory);
                        } catch (\Exception $e) { }

                        if (file_exists($filePathTemp)) {
                            // 判断是否备份成功(大于1K)，使用临时文件防止失败时被覆盖
                            if (filesize($filePathTemp) > 1024) {
                                copy($filePathTemp, $filePath);
                            }
                            unlink($filePathTemp);
                        }
                        if ($keepDays > 1) {
                            $this->_clearDirByKeepDays($dataBackupDir, $keepDays);
                        }
                    }
                }
            }

            // 其次增量备份项目文件
            if (!empty($backupConfig['file'])) {
                foreach ($backupConfig['file'] as $fileConfig) {
                    $fileBackupDir = "{$backupDir}/{$groupName}/{$fileConfig['host']}/file/";
                    if (!file_exists($fileBackupDir)) {
                        @mkdir($fileBackupDir, 0777, true);
                    }
                    foreach ($fileConfig['folders'] as $folderPath => $keepDays) {
                        Logger::log("Backing up folder: {$folderPath}", $this->logCategory);
                        $host = $clientConfig['host'];
                        $port = $clientConfig['port'];
                        $user = $clientConfig['user'];
                        $pass = $clientConfig['pass'];
                        $folderPath = rtrim($folderPath, '/');
                        $folderName = basename($folderPath);
                        try {
                            // 先把本地的目录做备份
                            if ($keepDays > 0) {
                                $backupDirPath = rtrim($fileBackupDir, '/').'/'.$folderName;
                                if (file_exists($backupDirPath)) {
                                    $this->_compressBackupFileDir($backupDirPath, date('Ymd', time() - 86400));
                                    $this->_clearDirByKeepDays($fileBackupDir, $keepDays, $folderName);
                                }
                            }
                            $ssh = new Lib_Network_Ssh($fileConfig['host'], $fileConfig['port'], $fileConfig['user'], $fileConfig['pass']);
                            // 先判断有没有安装sshpass工具，没有则自动安装
                            $result = $ssh->checkCmd('sshpass');
                            if (empty($result)) {
                                if (!empty($ssh->checkCmd('apt-get'))) {
                                    // Debian/Ubuntu 系统
                                    $ssh->syncCmd("echo \"{$pass}\" | sudo -S apt-get install -y sshpass");
                                } else if (!empty($ssh->checkCmd('yum'))) {
                                    // CentOS/RedHat 系统，注意这个时候只有root用户才能执行该命令
                                    $ssh->syncCmd("yum install -y sshpass");
                                } else {
                                    Logger::log("sshpass not installed, break", $this->logCategory);
                                }
                            }
                            $ssh->syncCmd("rsync -aurvz --delete -e 'sshpass -p {$pass} ssh -p {$port}' {$folderPath} {$user}@{$host}:{$fileBackupDir}");
                        } catch (\Exception $e) {
                            echo $e->getMessage().PHP_EOL;
                        }
                    }
                }
            }

            echo "Done!\n\n";
        }

        Logger::log('==================end====================', $this->logCategory);
    }

    /**
     * 压缩备份的文件目录
     *
     * @param string $backupFileDirPath 文件目录绝对路径
     * @param string $date              备份文件的日期
     *
     * @return void
     */
    private function _compressBackupFileDir($backupFileDirPath, $date)
    {
        $currentDirPath = getcwd();
        $dirPath = dirname($backupFileDirPath);
        $dirName = basename($backupFileDirPath);
        chdir($dirPath);
        exec("tar -cjvf {$dirPath}/{$dirName}.{$date}.tar.bz2 {$dirName}");
        chdir($currentDirPath);
    }

    /**
     * 按照给定天数清除超过保存期限的备份文件。
     *
     * @param string  $dirPath        备份目录绝对路径
     * @param integer $keepDays       保存天数
     * @param string  $fileNamePrefix 文件名前缀
     *
     * @return void
     */
    private function _clearDirByKeepDays($dirPath, $keepDays, $fileNamePrefix = null)
    {
        $files = array_diff(scandir($dirPath), array('..', '.'));
        // 只计算压缩文件的数量
        foreach ($files as $k => $file) {
            if (!empty($fileNamePrefix)) {
                if (!preg_match("/^{$fileNamePrefix}.+\.bz2/", $file)) {
                    unset($files[$k]);
                }
            } elseif (!preg_match('/.+\.bz2/', $file)) {
                unset($files[$k]);
            }
        }
        while (count($files) > $keepDays) {
            $file     = array_shift($files);
            $filePath = $dirPath.'/'.$file;
            Logger::log("Clearing expired file: {$filePath}", $this->logCategory);
            unlink($filePath);
        }
    }

}