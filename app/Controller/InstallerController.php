<?php
App::uses('AppController', 'Controller');

/**
 * Installer Controller
 */
class InstallerController extends AppController {
    var $uses = false;

    public function beforeFilter() {
        parent::beforeFilter();

        if(file_exists($this->appConfig['appInstallFile'])) {
            throw new BadRequestException('Jukebox database already installed. Please delete the following file and refresh this page to force a re-install: ' . $this->appConfig['appInstallFile']);
        }
    }

    public function index() {
        if(!is_dir($this->appConfig['wampMysqlDir'])) {
            $this->__writeInstallFile('Failed - could not locate mysql directory.');
        }

        $subDirs = scandir($this->appConfig['wampMysqlDir']);

        foreach($subDirs as $subDir) {
            if(substr($subDir, 0, 5) == 'mysql') {
                $mysqlExe = $this->appConfig['wampMysqlDir'] . DS . $subDir . DS . 'bin' . DS . 'mysql.exe';
                if(file_exists($mysqlExe)) {
                    $result = exec($mysqlExe . ' -u root < ' . $this->appConfig['appSchemaFile'], $lines);
                    if($result == 0) {
                        $this->__writeInstallFile('Success - database installed.');
                    }
                }
            }
        }

        $this->__writeInstallFile('Failed - could not locate mysql executable.');
    }

    private function __writeInstallFile($status) {
        file_put_contents($this->appConfig['appInstallFile'], date('Y-m-d H:i:s') . ' - ' . $this->appConfig['appVersion'] . ' - ' . $status);
        return $this->redirect('/');
    }
}
