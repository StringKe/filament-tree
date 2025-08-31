<?php

namespace StringKe\FilamentTree\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection as BaseCollection;

trait HasTree
{
    protected string $parentColumn = 'parent_id';

    protected string $childrenRelation = 'children';

    protected string $parentRelation = 'parent';

    public function initializeHasTree(): void
    {
        $this->fillable[] = $this->getParentColumn();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, $this->getParentColumn());
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, $this->getParentColumn());
    }

    public function ancestors(): Collection
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    public function descendants(): Collection
    {
        $descendants = collect();
        $children = $this->children;

        foreach ($children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }

    public function siblings(): Collection
    {
        return static::where($this->getParentColumn(), $this->getAttribute($this->getParentColumn()))
            ->where($this->getKeyName(), '!=', $this->getKey())
            ->get();
    }

    public function isRoot(): bool
    {
        return is_null($this->getAttribute($this->getParentColumn()));
    }

    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    public function isChildOf($parent): bool
    {
        return $this->getAttribute($this->getParentColumn()) === ($parent instanceof self ? $parent->getKey() : $parent);
    }

    public function isParentOf($child): bool
    {
        if ($child instanceof self) {
            return $child->getAttribute($this->getParentColumn()) === $this->getKey();
        }

        return static::where($this->getParentColumn(), $this->getKey())
            ->where($this->getKeyName(), $child)
            ->exists();
    }

    public function isAncestorOf($descendant): bool
    {
        if ($descendant instanceof self) {
            return $descendant->ancestors()->contains($this->getKeyName(), $this->getKey());
        }

        $node = static::find($descendant);

        return $node ? $node->ancestors()->contains($this->getKeyName(), $this->getKey()) : false;
    }

    public function isDescendantOf($ancestor): bool
    {
        $ancestorInstance = $ancestor instanceof self ? $ancestor : static::find($ancestor);

        return $ancestorInstance ? $ancestorInstance->isAncestorOf($this) : false;
    }

    public function getDepth(): int
    {
        return $this->ancestors()->count();
    }

    public function getRoot()
    {
        $ancestor = $this;

        while ($ancestor->parent) {
            $ancestor = $ancestor->parent;
        }

        return $ancestor;
    }

    public function getPath(string $separator = ' / ', string $field = 'name'): string
    {
        $path = $this->ancestors()->reverse()->pluck($field)->push($this->getAttribute($field));

        return $path->implode($separator);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull($this->getParentColumn());
    }

    public function scopeHasChildren(Builder $query): Builder
    {
        return $query->whereHas($this->childrenRelation);
    }

    public function scopeHasNoChildren(Builder $query): Builder
    {
        return $query->whereDoesntHave($this->childrenRelation);
    }

    public function scopeIsLeaf(Builder $query): Builder
    {
        return $query->hasNoChildren();
    }

    public function scopeWithDepth(Builder $query): Builder
    {
        $table = $this->getTable();
        $key = $this->getKeyName();
        $parentColumn = $this->getParentColumn();

        $query->withCount([
            $this->parentRelation => function ($query) use ($table, $key, $parentColumn) {
                $query->from($table . ' as ancestors')
                    ->selectRaw('count(*)')
                    ->whereColumn('ancestors.' . $key, $table . '.' . $parentColumn);
            },
        ]);

        return $query;
    }

    public static function tree(): Collection
    {
        $items = static::with('children')->get();

        return static::buildTree($items);
    }

    public static function buildTree(Collection $items, $parentId = null): Collection
    {
        $tree = new Collection;
        $parentColumn = (new static)->getParentColumn();

        foreach ($items as $item) {
            if ($item->getAttribute($parentColumn) == $parentId) {
                $children = static::buildTree($items, $item->getKey());
                if ($children->isNotEmpty()) {
                    $item->setRelation('children', $children);
                }
                $tree->push($item);
            }
        }

        return $tree;
    }

    public static function flatTree(): BaseCollection
    {
        $tree = static::tree();

        return static::flattenTree($tree);
    }

    protected static function flattenTree(Collection $tree, int $depth = 0): BaseCollection
    {
        $flat = collect();

        foreach ($tree as $node) {
            $node->depth = $depth;
            $flat->push($node);

            if ($node->relationLoaded('children') && $node->children->isNotEmpty()) {
                $flat = $flat->merge(static::flattenTree($node->children, $depth + 1));
            }
        }

        return $flat;
    }

    public function moveToParent($parent): bool
    {
        $parentId = $parent instanceof self ? $parent->getKey() : $parent;

        if ($this->getKey() === $parentId) {
            return false;
        }

        if ($parentId && $this->isAncestorOf($parentId)) {
            return false;
        }

        $this->setAttribute($this->getParentColumn(), $parentId);

        return $this->save();
    }

    public function makeRoot(): bool
    {
        $this->setAttribute($this->getParentColumn(), null);

        return $this->save();
    }

    protected function getParentColumn(): string
    {
        return $this->parentColumn ?? 'parent_id';
    }

    protected function getChildrenRelation(): string
    {
        return $this->childrenRelation ?? 'children';
    }

    protected function getParentRelation(): string
    {
        return $this->parentRelation ?? 'parent';
    }
}
