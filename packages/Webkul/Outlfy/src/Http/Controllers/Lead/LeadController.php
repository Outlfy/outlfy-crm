<?php

namespace Webkul\Outlfy\Http\Controllers\Lead;

use Webkul\Admin\Http\Controllers\Lead\LeadController as BaseLeadController;
use Illuminate\Http\JsonResponse;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\Http\Resources\LeadResource;
use Webkul\Admin\Http\Resources\StageResource;
use Webkul\Lead\Repositories\LeadRepository;

class LeadController extends BaseLeadController
{
    /**
     * Returns a listing of the resource.
     */
    public function get(): JsonResponse
    {
        if (request()->query('pipeline_id')) {
            $pipeline = $this->pipelineRepository->find(request()->query('pipeline_id'));
        } else {
            $pipeline = $this->pipelineRepository->getDefaultPipeline();
        }

        if ($stageId = request()->query('pipeline_stage_id')) {
            $stages = $pipeline->stages->where('id', request()->query('pipeline_stage_id'));
        } else {
            $stages = $pipeline->stages;
        }

        if ($org = bouncer()->getUserOrganisation()) {

        }

        foreach ($stages as $stage) {
            /**
             * We have to create a new instance of the lead repository every time, which is
             * why we're not using the injected one.
             */
            $query = app(LeadRepository::class)
                ->pushCriteria(app(RequestCriteria::class))
                ->where([
                    'lead_pipeline_id'       => $pipeline->id,
                    'lead_pipeline_stage_id' => $stage->id,
                ]);

            if ($userIds = bouncer()->getAuthorizedUserIds()) {
                $query->whereIn('leads.user_id', $userIds);
            }

            if ($org) {
                $query->where('organisation_id', $org);
            }

            $stage->lead_value = (clone $query)->sum('lead_value');

            $data[$stage->sort_order] = (new StageResource($stage))->jsonSerialize();

            $data[$stage->sort_order]['leads'] = [
                'data' => LeadResource::collection($paginator = $query->with([
                    'tags',
                    'type',
                    'source',
                    'user',
                    'person',
                    'person.organization',
                    'pipeline',
                    'pipeline.stages',
                    'stage',
                    'attribute_values',
                ])->paginate(10)),

                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'from'         => $paginator->firstItem(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'to'           => $paginator->lastItem(),
                    'total'        => $paginator->total(),
                ],
            ];
        }

        return response()->json($data);
    }
}
