<?php

namespace StringKe\FilamentTree\Forms\Components;

use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TreeSelect extends Select
{
    protected bool $showTree = true;
    
    protected int $indentSize = 20;
    
    protected string $indentCharacter = '└─';
    
    protected ?int $maxDepth = null;
    
    protected bool $showPath = false;
    
    protected string $pathSeparator = ' / ';
    
    protected string $pathField = 'name';

    protected function setUp(): void
    {
        parent::setUp();

        $this->view('filament-tree::forms.components.tree-select');

        $this->getSearchResultsUsing(function (string $search): array {
            return $this->getTreeOptions($search);
        });

        $this->getOptionLabelsUsing(function (array $values): array {
            return $this->getSelectedLabels($values);
        });
    }

    public function showTree(bool $show = true): static
    {
        $this->showTree = $show;
        return $this;
    }

    public function indentSize(int $size): static
    {
        $this->indentSize = $size;
        return $this;
    }

    public function indentCharacter(string $character): static
    {
        $this->indentCharacter = $character;
        return $this;
    }

    public function maxDepth(?int $depth): static
    {
        $this->maxDepth = $depth;
        return $this;
    }

    public function showPath(bool $show = true): static
    {
        $this->showPath = $show;
        return $this;
    }

    public function pathSeparator(string $separator): static
    {
        $this->pathSeparator = $separator;
        return $this;
    }

    public function pathField(string $field): static
    {
        $this->pathField = $field;
        return $this;
    }

    public function relationship(string|Closure $name, string|Closure $titleAttribute, ?Closure $modifyQueryUsing = null): static
    {
        parent::relationship($name, $titleAttribute, $modifyQueryUsing);

        if (!$this->hasOptionsList()) {
            $this->options(function () use ($titleAttribute) {
                return $this->getTreeOptions();
            });
        }

        return $this;
    }

    protected function getTreeOptions(?string $search = null): array
    {
        $relationship = $this->getRelationship();

        if (!$relationship) {
            return [];
        }

        $query = $relationship->getRelated()->query();

        if ($search) {
            $titleAttribute = $this->getRelationshipTitleAttribute();
            $query->where($titleAttribute, 'like', '%' . $search . '%');
        }

        $items = $query->get();
        
        if (method_exists($relationship->getRelated(), 'buildTree')) {
            $tree = $relationship->getRelated()::buildTree($items);
        } else {
            $tree = $this->buildTree($items);
        }

        return $this->formatTreeOptions($tree);
    }

    protected function buildTree(Collection $items, $parentId = null): Collection
    {
        $tree = new Collection();

        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);
                if ($children->isNotEmpty()) {
                    $item->setRelation('children', $children);
                }
                $tree->push($item);
            }
        }

        return $tree;
    }

    protected function formatTreeOptions(Collection $tree, int $depth = 0): array
    {
        $options = [];

        if ($this->maxDepth && $depth >= $this->maxDepth) {
            return $options;
        }

        foreach ($tree as $node) {
            $label = $this->formatNodeLabel($node, $depth);
            $options[$node->getKey()] = $label;

            if ($node->relationLoaded('children') && $node->children->isNotEmpty()) {
                $childOptions = $this->formatTreeOptions($node->children, $depth + 1);
                $options = array_merge($options, $childOptions);
            }
        }

        return $options;
    }

    protected function formatNodeLabel(Model $node, int $depth): string
    {
        $titleAttribute = $this->getRelationshipTitleAttribute();
        $label = $node->getAttribute($titleAttribute);

        if ($this->showPath && method_exists($node, 'getPath')) {
            return $node->getPath($this->pathSeparator, $this->pathField);
        }

        if ($this->showTree && $depth > 0) {
            $indent = str_repeat('　', $depth - 1);
            $label = $indent . $this->indentCharacter . ' ' . $label;
        }

        return $label;
    }

    protected function getSelectedLabels(array $values): array
    {
        $relationship = $this->getRelationship();

        if (!$relationship) {
            return [];
        }

        $titleAttribute = $this->getRelationshipTitleAttribute();
        $models = $relationship->getRelated()->whereIn('id', $values)->get();

        $labels = [];
        foreach ($models as $model) {
            if ($this->showPath && method_exists($model, 'getPath')) {
                $labels[$model->getKey()] = $model->getPath($this->pathSeparator, $this->pathField);
            } else {
                $labels[$model->getKey()] = $model->getAttribute($titleAttribute);
            }
        }

        return $labels;
    }

    public function getShowTree(): bool
    {
        return $this->showTree;
    }

    public function getIndentSize(): int
    {
        return $this->indentSize;
    }

    public function getIndentCharacter(): string
    {
        return $this->indentCharacter;
    }

    public function getMaxDepth(): ?int
    {
        return $this->maxDepth;
    }

    public function getShowPath(): bool
    {
        return $this->showPath;
    }

    public function getPathSeparator(): string
    {
        return $this->pathSeparator;
    }

    public function getPathField(): string
    {
        return $this->pathField;
    }
}