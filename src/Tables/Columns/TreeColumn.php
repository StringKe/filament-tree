<?php

namespace StringKe\FilamentTree\Tables\Columns;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class TreeColumn extends TextColumn
{
    protected bool $showIndent = true;
    
    protected int $indentSize = 20;
    
    protected string $expandIcon = 'heroicon-m-chevron-right';
    
    protected string $collapseIcon = 'heroicon-m-chevron-down';
    
    protected string $leafIcon = 'heroicon-m-minus';
    
    protected bool $showIcon = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->view('filament-tree::columns.tree-column');
    }

    public function showIndent(bool $show = true): static
    {
        $this->showIndent = $show;
        return $this;
    }

    public function indentSize(int $size): static
    {
        $this->indentSize = $size;
        return $this;
    }

    public function expandIcon(string $icon): static
    {
        $this->expandIcon = $icon;
        return $this;
    }

    public function collapseIcon(string $icon): static
    {
        $this->collapseIcon = $icon;
        return $this;
    }

    public function leafIcon(string $icon): static
    {
        $this->leafIcon = $icon;
        return $this;
    }

    public function showIcon(bool $show = true): static
    {
        $this->showIcon = $show;
        return $this;
    }

    public function getShowIndent(): bool
    {
        return $this->showIndent;
    }

    public function getIndentSize(): int
    {
        return $this->indentSize;
    }

    public function getExpandIcon(): string
    {
        return $this->expandIcon;
    }

    public function getCollapseIcon(): string
    {
        return $this->collapseIcon;
    }

    public function getLeafIcon(): string
    {
        return $this->leafIcon;
    }

    public function getShowIcon(): bool
    {
        return $this->showIcon;
    }

    public function getDepth(Model $record): int
    {
        if (method_exists($record, 'getDepth')) {
            return $record->getDepth();
        }

        if (isset($record->depth)) {
            return $record->depth;
        }

        return 0;
    }

    public function hasChildren(Model $record): bool
    {
        if (method_exists($record, 'children')) {
            return $record->children()->exists();
        }

        return false;
    }

    public function isExpanded(Model $record): bool
    {
        return false;
    }
}