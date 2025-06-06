<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Modules\HelpGuide\ErrorLogsViewer\Level;
use Modules\HelpGuide\ErrorLogsViewer\Pattern;
use Modules\HelpGuide\ErrorLogsViewer\LogViewer;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Crypt;

/**
 * @group Error logs
 *
 */
class ErrorLogsController extends Controller
{
    /**
     * @var
     */
    protected $request;
    /**
     * @var 
     */
    private $log_viewer;

    /**
     * @var string
     */
    protected $view_log = 'helpguide::dashboard.error_logs.index';
	
    /**
     * constructor.
     */
    public function __construct()
    {
        $this->log_viewer = new LogViewer();
        $this->request = app('request');
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function index()
    {

        $folderFiles = [];

        if ($this->request->input('f')) {
            $this->log_viewer->setFolder(Crypt::decrypt($this->request->input('f')));
            $folderFiles = $this->log_viewer->getFolderFiles(true);
        }

        $currentFile = "";
        if ($this->request->input('l')) {
            if ($this->request->input('f')) {
                $currentFile = Crypt::decrypt($this->request->input('f')).'-';
                $this->log_viewer->setFolder(Crypt::decrypt($this->request->input('f')));
            }
            $currentFile .= Crypt::decrypt($this->request->input('l'));
            $this->log_viewer->setFile(Crypt::decrypt($this->request->input('l')));

        }

        if ($early_return = $this->earlyReturn()) {
            return $early_return;
        }

        $data = [
            'logs' => $this->log_viewer->all(),
            'folders' => $this->log_viewer->getFolders(),
            'current_folder' => $this->log_viewer->getFolderName(),
            'folder_files' => $folderFiles,
            'files' => $this->log_viewer->getFiles(true),
            'current_file' => $currentFile,
            'standardFormat' => true,
        ];

        if ($this->request->wantsJson()) {
            return $data;
        }

        if (is_array($data['logs']) && count($data['logs']) > 0) {
            $firstLog = reset($data['logs']);
            if (!$firstLog['context'] && !$firstLog['level']) {
                $data['standardFormat'] = false;
            }
        }

        return app('view')->make($this->view_log, $data);
    }

    /**
     * @return bool|mixed
     * @throws \Exception
     */
    private function earlyReturn()
    {
        if ($this->request->input('f')) {
            $this->log_viewer->setFolder(Crypt::decrypt($this->request->input('f')));
        }

        if ($this->request->input('dl')) {
            return $this->download($this->pathFromInput('dl'));
        } elseif ($this->request->has('clean')) {
            app('files')->put($this->pathFromInput('clean'), '');
            return $this->redirect($this->request->url());
        } elseif ($this->request->has('del')) {
            app('files')->delete($this->pathFromInput('del'));
            return $this->redirect($this->request->url());
        } elseif ($this->request->has('delall')) {
            $files = ($this->log_viewer->getFolderName())
                        ? $this->log_viewer->getFolderFiles(true)
                        : $this->log_viewer->getFiles(true);
            foreach ($files as $file) {
                app('files')->delete($this->log_viewer->pathToLogFile($file));
            }
            return $this->redirect($this->request->url());
        }
        return false;
    }

    /**
     * @param string $input_string
     * @return string
     * @throws \Exception
     */
    private function pathFromInput($input_string)
    {
        return $this->log_viewer->pathToLogFile(Crypt::decrypt($this->request->input($input_string)));
    }

    /**
     * @param $to
     * @return mixed
     */
    private function redirect($to)
    {
        if (function_exists('redirect')) {
            return redirect($to);
        }

        return app('redirect')->to($to);
    }

    /**
     * @param string $data
     * @return mixed
     */
    private function download($data)
    {
        if (function_exists('response')) {
            return response()->download($data);
        }

        // For laravel 4.2
        return app('\Illuminate\Support\Facades\Response')->download($data);
    }
}