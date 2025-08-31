export default function filamentTree() {
    return {
        expanded: {},
        defaultExpanded: false,
        maxDepth: null,
        
        init() {
            this.defaultExpanded = this.$el.dataset.defaultExpanded === 'true';
            this.maxDepth = this.$el.dataset.maxDepth ? parseInt(this.$el.dataset.maxDepth) : null;
            
            if (this.defaultExpanded) {
                this.expandAll();
            }
            
            this.$watch('expanded', () => {
                this.saveExpandedState();
            });
            
            this.loadExpandedState();
        },
        
        toggle(id) {
            this.expanded[id] = !this.expanded[id];
        },
        
        isExpanded(id) {
            return this.expanded[id] || false;
        },
        
        hasChildren(id) {
            const element = document.querySelector(`[data-tree-parent="${id}"]`);
            return element !== null;
        },
        
        expandAll() {
            const allNodes = document.querySelectorAll('[data-tree-id]');
            allNodes.forEach(node => {
                const id = node.dataset.treeId;
                if (this.hasChildren(id)) {
                    this.expanded[id] = true;
                }
            });
        },
        
        collapseAll() {
            this.expanded = {};
        },
        
        expandToLevel(level) {
            const allNodes = document.querySelectorAll('[data-tree-depth]');
            allNodes.forEach(node => {
                const depth = parseInt(node.dataset.treeDepth);
                const id = node.dataset.treeId;
                if (depth < level && this.hasChildren(id)) {
                    this.expanded[id] = true;
                } else {
                    this.expanded[id] = false;
                }
            });
        },
        
        saveExpandedState() {
            const tableId = this.$el.dataset.tableId;
            if (tableId) {
                localStorage.setItem(`filament-tree-expanded-${tableId}`, JSON.stringify(this.expanded));
            }
        },
        
        loadExpandedState() {
            const tableId = this.$el.dataset.tableId;
            if (tableId) {
                const saved = localStorage.getItem(`filament-tree-expanded-${tableId}`);
                if (saved) {
                    try {
                        this.expanded = JSON.parse(saved);
                    } catch (e) {
                        console.error('Failed to load expanded state:', e);
                    }
                }
            }
        },
        
        getIndentStyle(depth) {
            const indentSize = this.$el.dataset.indentSize || 20;
            return `padding-left: ${depth * indentSize}px`;
        },
        
        isVisible(parentId, depth = 0) {
            if (!parentId) return true;
            
            if (this.maxDepth && depth > this.maxDepth) {
                return false;
            }
            
            const parentElement = document.querySelector(`[data-tree-id="${parentId}"]`);
            if (!parentElement) return false;
            
            const grandParentId = parentElement.dataset.treeParent;
            
            if (!this.isExpanded(parentId)) {
                return false;
            }
            
            if (grandParentId) {
                return this.isVisible(grandParentId, depth + 1);
            }
            
            return true;
        },
        
        handleDragStart(event, id) {
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', id);
            event.target.classList.add('dragging');
        },
        
        handleDragEnd(event) {
            event.target.classList.remove('dragging');
        },
        
        handleDragOver(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            
            const afterElement = this.getDragAfterElement(event.currentTarget, event.clientY);
            const dragging = document.querySelector('.dragging');
            
            if (afterElement == null) {
                event.currentTarget.appendChild(dragging);
            } else {
                event.currentTarget.insertBefore(dragging, afterElement);
            }
        },
        
        handleDrop(event, targetId) {
            event.preventDefault();
            const sourceId = event.dataTransfer.getData('text/plain');
            
            if (sourceId !== targetId) {
                this.$wire.moveNode(sourceId, targetId);
            }
        },
        
        getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('[draggable]:not(.dragging)')];
            
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }
    };
}

window.filamentTree = filamentTree;