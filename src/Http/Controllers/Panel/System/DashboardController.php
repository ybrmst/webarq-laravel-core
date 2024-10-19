<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 12/19/2016
 * Time: 10:28 AM
 */

namespace Webarq\Http\Controllers\Panel\System;


use Wa;
use Webarq\Http\Controllers\Panel\BaseController;
use Webarq\Model\HistoryModel;

class DashboardController extends BaseController
{
    protected $adminLevel;

    public function before()
    {
        $this->adminLevel = $this->admin->getLevel(true);

        view()->share('shareSearchBox', false);
    }

    public function actionGetIndex()
    {
        $this->layout->{'rightSection'} = view('webarq::system.dashboard.index', [
                'dailyActivity' => $this->roleModel()->daily()->where('role_level', '>=', $this->adminLevel)->count('id'),
                'weeklyActivity' => $this->roleModel()->weekly()->where('role_level', '>=', $this->adminLevel)->count('id'),
                'monthlyActivity' => $this->roleModel()->monthly()->where('role_level', '>=', $this->adminLevel)->count('id')
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function roleModel()
    {
        if (!$this->admin->isDaemon()) {
            return HistoryModel::where('role_level', '>=', $this->adminLevel)->where('role_level', '>', 0);
        } else {
            return HistoryModel::where('role_level', '>=', 0);
        }
    }

    public function actionGetDetail()
    {
        switch ($this->getParam(1)) {
            case 'monthly':
                $activities = $this->roleModel()->monthly();
                break;
            case 'weekly':
                $activities = $this->roleModel()->weekly();
                break;
            case 'daily':
                $activities = $this->roleModel()->daily();
                break;
            default:
                $activities = $this->roleModel();
                break;
        }

        $activities = $activities
                ->orderBy('create_on', 'desc')
                ->paginate(20);

        $table = \Wa::html('table')
                ->setTitle(studly_case($this->getParam(1)) . ' Activities',
                        '<div class="box-header with-border"><h3 class="box-title"></h3></div>')
                ->setContainer('table', 'table', ['class' => 'table table-bordered']);

// Add head
        $table->addHead()->addRow()->addCell('Actor')->addCell('Description')->addCell('Date');
// Add body
        if ($activities->count()) {
            $body = $table->addBody();
            foreach ($activities as $activity) {
                $body->addRow()
                        ->addCell($activity->actor)
                        ->addCell(Wa::manager('cms.history')->formatting($activity, $activity->action))
                        ->addCell(Wa::modifier('wa-date:M d, Y', $activity->create_on));

            }
        } else {
            $table->addBody()->addRow()->addCell(Wa::trans('webarq::core.messages.item-not-found'), ['colspan' => 3]);
        }
        $this->layout->{'rightSection'} = $table->toHtml()
                . $activities->render(
                        Wa::getThemesView(config('webarq.system.themes', 'default'), 'common.pagination', false)
                );
    }

    protected function isAccessible()
    {
        return true;
    }
}