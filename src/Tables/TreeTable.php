<?php

namespace StringKe\FilamentTree\Tables;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TreeTable
{
    protected Table $table;

    protected bool $defaultExpanded = false;

    protected ?int $maxDepth = null;

    protected string $parentColumn = 'parent_id';

    protected string $childrenRelation = 'children';

    public function __construct(Table $table)
    {
        $this->table = $table;
        $this->configure();
    }

    public static function make(Table $table): static
    {
        return new static($table);
    }

    protected function configure(): void
    {
        $this->table->modifyQueryUsing(function (Builder $query) {
            return $query->whereNull($this->parentColumn)->with($this->childrenRelation);
        });

        $this->table->recordAction(null);

        $this->table->contentGrid(null);
    }

    public function defaultExpanded(bool $expanded = true): static
    {
        $this->defaultExpanded = $expanded;

        return $this;
    }

    public function maxDepth(?int $depth): static
    {
        $this->maxDepth = $depth;

        return $this;
    }

    public function parentColumn(string $column): static
    {
        $this->parentColumn = $column;

        return $this;
    }

    public function childrenRelation(string $relation): static
    {
        $this->childrenRelation = $relation;

        return $this;
    }

    public function columns(array $columns): static
    {
        $this->table->columns($columns);

        return $this;
    }

    public function filters(array $filters): static
    {
        $this->table->filters($filters);

        return $this;
    }

    public function actions(array $actions): static
    {
        $this->table->actions($actions);

        return $this;
    }

    public function bulkActions(array $actions): static
    {
        $this->table->bulkActions($actions);

        return $this;
    }

    public function defaultSort(string $column, string $direction = 'asc'): static
    {
        $this->table->defaultSort($column, $direction);

        return $this;
    }

    public function reorderable(?string $column = 'sort'): static
    {
        $this->table->reorderable($column);

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->table->searchable($searchable);

        return $this;
    }

    public function paginated(bool | array $paginated = true): static
    {
        $this->table->paginated($paginated);

        return $this;
    }

    public function poll(?string $interval): static
    {
        $this->table->poll($interval);

        return $this;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function isDefaultExpanded(): bool
    {
        return $this->defaultExpanded;
    }

    public function getMaxDepth(): ?int
    {
        return $this->maxDepth;
    }

    public function getParentColumn(): string
    {
        return $this->parentColumn;
    }

    public function getChildrenRelation(): string
    {
        return $this->childrenRelation;
    }
}
