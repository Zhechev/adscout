<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * PlayerQuery class handles the logic for filtering player queries based on request parameters.
 * It is designed to work with the Laravel query builder and request classes.
 */
class PlayerQuery
{
    /**
     * The Eloquent query builder instance.
     *
     * @var Builder
     */
    protected $query;

    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Constructor for the PlayerQuery class.
     *
     * @param Builder $query   The Eloquent query builder.
     * @param Request $request The HTTP request.
     */
    public function __construct(Builder $query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    /**
     * Applies filters to the query based on the request parameters.
     *
     * @return Builder
     */
    public function filter(): Builder
    {
        return $this->filterPosition()
                    ->filterName()
                    ->filterTeamId()
                    ->getQuery();
    }

    /**
     * Filter by position if the position parameter is provided.
     *
     * @return self
     */
    protected function filterPosition(): self
    {
        if ($this->request->has('position')) {
            $this->query->where('position', $this->request->input('position'));
        }

        return $this;
    }

    /**
     * Filter by name using 'like' query if the name parameter is provided.
     *
     * @return self
     */
    protected function filterName(): self
    {
        if ($this->request->has('name')) {
            $this->query->where('name', 'like', '%' . $this->request->input('name') . '%');
        }

        return $this;
    }

    /**
     * Filter by team ID if the team_id parameter is provided.
     *
     * @return self
     */
    protected function filterTeamId(): self
    {
        if ($this->request->has('team_id')) {
            $this->query->where('team_id', $this->request->input('team_id'));
        }

        return $this;
    }

    /**
     * Get the modified query.
     *
     * @return Builder
     */
    protected function getQuery(): Builder
    {
        return $this->query;
    }
}
