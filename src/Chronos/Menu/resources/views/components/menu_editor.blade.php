<script type="text/x-template" id="menu-item-template">
    <li>
        <div class="menu-item" v-bind:class="{ minimized : isMinimized }">
            <div class="reorder" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'items.' + menuOrder + '.name') }" v-on:mousedown="dragStart" v-on:mouseout="dragEnd"><div class="drag"></div><span v-html="name" v-if="name"></span><em v-if="!name">{!! trans('chronos.menu::forms.Unnamed menu item') !!}</em></div>
            <a v-bind:class="{minimize: !isMinimized, maximize: isMinimized}" v-on:click="toggleItem"></a>
            <a class="delete" v-on:click="deleteMenuItem"></a>
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'items.' + menuOrder + '.name') }">
                        <label class="control-label" v-bind:for="'items.' + menuOrder + '.name'">{!! trans('chronos.menu::forms.Navigation item label') !!}</label>
                        <input class="form-control" v-bind:id="'items.' + menuOrder + '.name'" v-bind:name="'items[' + menuOrder + '][name]'" type="text" v-model="name" />
                        <span class="help-block" v-html="store.formErrors['items'][menuOrder]['name'][0]" v-if="Object.hasKey(store.formErrors, 'items.' + menuOrder + '.name')"></span>
                        <span class="help-block" v-else>{!! trans('chronos.menu::forms.Original') !!}: @{{ originalName }}</span>
                        <input v-bind:name="'items[' + menuOrder + '][original_name]'" type="hidden" v-model="originalName" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'items.' + menuOrder + '.url_anchor') }">
                        <label class="control-label" v-bind:for="'items.' + menuOrder + '.url_anchor'">{!! trans('chronos.menu::forms.Anchor') !!}</label>
                        <input class="form-control" v-bind:id="'items.' + menuOrder + '.url_anchor'" v-bind:name="'items[' + menuOrder + '][url_anchor]'" type="text" v-model="urlAnchor" />
                        <span class="help-block" v-html="store.formErrors['items'][menuOrder]['url_anchor'][0]" v-if="Object.hasKey(store.formErrors, 'items.' + menuOrder + '.url_anchor')"></span>
                        <span class="help-block" v-else>{!! trans('chronos.menu::forms.Specifies location to jump to on page. E.g.: #top') !!}</span>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'items.' + menuOrder + '.url_target') }">
                        <label class="control-label" v-bind:for="'items.' + menuOrder + '.url_target'">{!! trans('chronos.menu::forms.Target') !!}</label>
                        <input class="form-control" v-bind:id="'items.' + menuOrder + '.url_target'" v-bind:name="'items[' + menuOrder + '][url_target]'" type="text" v-model="urlTarget" />
                        <span class="help-block" v-html="store.formErrors['items'][menuOrder]['url_target'][0]" v-if="Object.hasKey(store.formErrors, 'items.' + menuOrder + '.url_target')"></span>
                        <span class="help-block" v-else>{!! trans('chronos.menu::forms.Specifies target window in which to open link. E.g.: _blank') !!}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <label class="control-label" v-bind:for="'items.' + menuOrder + '.status'">{!! trans('chronos.menu::forms.Active?') !!}</label>
                        <div class="checkbox">
                            <label>
                                <input v-bind:id="'items.' + menuOrder + '.status'" v-bind:name="'items[' + menuOrder + '][status]'" type="checkbox" v-model="status" />
                                {!! trans('chronos.menu::forms.Yes') !!}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <input v-bind:name="'items[' + menuOrder + '][order]'" type="hidden" v-model="menuOrder" />
            <input v-bind:name="'items[' + menuOrder + '][url_type]'" type="hidden" v-model="urlType" />
            <input v-bind:name="'items[' + menuOrder + '][url_value]'" type="hidden" v-model="urlValue" />

            <div class="menu-item-actions">
                <button class="btn btn-primary" v-on:click="saveItem">{!! trans('chronos.menu::forms.Save') !!}</button>
            </div>
        </div>
        <ul class="menu-item-list">
            <one-menu-item v-for="(item, key) in items" v-bind:item-data="item" v-bind:items="item.items" v-bind:key="item.key" v-bind:menu-order="item.menuOrder" v-if="items && items.length > 0"></one-menu-item>
        </ul>
    </li>
</script>

<script>
    var dragItemEventHub = new Vue();
    var editorEventHub = new Vue();

    Vue.component('menu-editor', {
        components: {
            oneMenuItem: {
                created: function() {
                    // populate with data
                    if (this.itemData) {
                        this.depth = this.itemData.depth;
                        this.id = this.itemData.id;
                        this.isMinimized = this.itemData.isMinimized;
                        this.menuId = this.itemData.menuId;
                        this.name = this.itemData.name;
                        this.originalName = this.itemData.originalName;
                        this.parentId = this.itemData.parentId;
                        this.status = this.itemData.status;
                        this.uid = this.itemData.uid;
                        this.urlAnchor = this.itemData.urlAnchor;
                        this.urlTarget = this.itemData.urlTarget;
                        this.urlType = this.itemData.urlType;
                        this.urlValue = this.itemData.urlValue;
                    }
                },
                data: function() {
                    return {
                        depth: 0,
                        id: null,
                        isMinimized: true,
                        menuId: null,
                        name: '',
                        originalName: '',
                        parentId: null,
                        status: 1,
                        store: vueStore.state,
                        uid: '',
                        urlAnchor: '',
                        urlTarget: null,
                        urlType: '',
                        urlValue: ''
                    }
                },
                methods: {
                    deleteMenuItem: function() {
                        editorEventHub.$emit('delete-item', this);
                    },
                    dragDrop: function(event) {
                        event.stopPropagation();

                        [].forEach.call(document.querySelectorAll('.menu-item'), function(item) {
                            item.classList.remove('dragover');

                            [].forEach.call(item.childNodes, function(child) {
                                if (child.tagName)
                                    child.style.pointerEvents = 'auto';
                            });
                        });

                        dragItemEventHub.$emit('reorder-elements', this, event.offsetX);
                    },
                    dragEnd: function(event) {
                        event.stopPropagation();

                        var target = event.target.parentElement.parentElement;

                        target.removeAttribute('draggable');
                        target.classList.remove('dragged');

                        [].forEach.call(document.querySelectorAll('.menu-item'), function(item) {
                            item.classList.remove('dragover');

                            [].forEach.call(item.childNodes, function(child) {
                                if (child.tagName)
                                    child.style.pointerEvents = 'auto';
                            });
                        });
                    },
                    dragEnter: function(event) {
                        event.preventDefault();

                        event.target.classList.add('dragover');
                    },
                    dragLeave: function(event) {
                            event.target.classList.remove('dragover', 'indent');
                    },
                    dragOver: function(event) {
                        event.preventDefault();

                        event.target.classList.remove('indent');

                        if (event.offsetX > 30) {
                            event.target.classList.add('indent');
                        }

                        return false;
                    },
                    dragStart: function(event) {
                        event.stopPropagation();

                        var target = event.target.parentElement.parentElement;

                        if (!target.classList.contains('menu-item'))
                            return;

                        target.classList.add('dragged');
                        target.setAttribute('draggable', 'true');

                        [].forEach.call(document.querySelectorAll('.menu-item'), function(item) {
                            if (item !== target) {
                                [].forEach.call(item.childNodes, function (child) {
                                    if (child.tagName)
                                        child.style.pointerEvents = 'none';
                                });
                            }
                        });

                        dragItemEventHub.$emit('set-drag-element', this);
                    },
                    saveItem: function() {
                        vm.$emit('show-loader');

                        this.$http({
                            body: {
                                name: this.name,
                                status: this.status,
                                url_anchor: this.urlAnchor,
                                url_target: this.urlTarget
                            },
                            method: 'PATCH',
                            url: '/api/menus/item/' + this.menuId + '/' + this.id
                        }).then(function (response) {
                            if (response.body.alerts) {
                                response.body.alerts.forEach(function (alert) {
                                    vm.$emit('add-alert', alert);
                                }.bind(this));
                            }

                            vm.$emit('hide-loader');
                        }, function (response) {
                            vueStore.updateFormErrors(response.body);

                            if (response.body.alerts) {
                                response.body.alerts.forEach(function (alert) {
                                    vm.$emit('add-alert', alert);
                                }.bind(this));
                            }
                            else {
                                vm.$emit('add-alert', {
                                    type: 'error',
                                    title: 'AJAX error',
                                    message: response.statusText + ' (' + response.status + ')'
                                });
                            }

                            vm.$emit('hide-loader');
                        });
                    },
                    toggleItem: function() {
                        this.isMinimized = !this.isMinimized;
                    }
                },
                mounted: function() {
                    this.$el.addEventListener('dragenter', this.dragEnter, false);
                    this.$el.addEventListener('dragleave', this.dragLeave, false);
                    this.$el.addEventListener('dragover', this.dragOver, false);
                    this.$el.addEventListener('drop', this.dragDrop, false);
                },
                name: 'one-menu-item',
                props: {
                    itemData: {
                        type: Object
                    },
                    items: {
                        default: [],
                        type: Array
                    },
                    menuOrder: {
                        default: 0,
                        type: Number
                    }
                },
                template: '#menu-item-template'
            }
        },
        created: function () {
            // populate data
            this.getData();

            // get languages
            if (this.menuId == null)
                this.getLanguages();

            // add listeners
            dragItemEventHub.$on('reorder-elements', this.reorderElements);
            dragItemEventHub.$on('set-drag-element', this.setDragElement);

            editorEventHub.$on('delete-item', this.deleteMenuItem);
        },
        data: function() {
            return {
                contentLoader: false,
                contentTypes: [],
                contentTypesLoader: false,
                customUrl: {
                    text: '',
                    url: ''
                },
                data: '',
                dragElement: null,
                languages: '',
                menu: '',
                menuLoader: false,
                menuRename: false,
                newMenuName: '',
                openTab: null,
                searchResults: [],
                searchTerm: '',
                selectedContent: [],
                store: vueStore.state
            }
        },
        methods: {
            addToMenu: function(typeId) {
                this.menuLoader = true;

                // Content
                if (typeId !== 0) {
                    this.selectedContent.forEach(function(item) {
                        this.$http({
                            body: {
                                'name': this.searchResults[typeId][item].title,
                                'url_type': 'Content',
                                'url_value': this.searchResults[typeId][item].id
                            },
                            method: 'POST',
                            url: '/api/menus/item/' + this.menu.id
                        }).then(function(response) {
                            var content = response.body;

                            if (content) {
                                this.menu.items.push({
                                    depth: content.item.depth,
                                    id: content.item.id,
                                    isMinimized: true,
                                    items: [],
                                    key: content.item.order,
                                    menuId: content.item.menu_id,
                                    name: content.item.name,
                                    order: content.item.order,
                                    originalName: content.item.original_name,
                                    parentId: content.item.parent_id,
                                    status: content.item.status,
                                    urlAnchor: content.item.url_anchor,
                                    urlTarget: content.item.url_target,
                                    urlType: content.item.url_type,
                                    urlValue: content.item.url_value
                                });
                            }

                            this.searchResults = [];
                            this.searchTerm = '';
                            this.selectedContent = [];

                            this.menuLoader = false;
                        }, function(response) {
                            vueStore.updateFormErrors(response.body);

                            if (response.body.alerts) {
                                response.body.alerts.forEach(function(alert) {
                                    vm.$emit('add-alert', alert);
                                }.bind(this));
                            }
                            else {
                                vm.$emit('add-alert', {
                                    type: 'error',
                                    title: 'AJAX error',
                                    message: response.statusText + ' (' + response.status + ')'
                                });
                            }

                            this.menuLoader = false;
                        });
                    }.bind(this));
                }
                // Custom link
                else {
                    this.$http({
                        body: {
                            'name': this.customUrl.text,
                            'url_type': 'URL',
                            'url_value': this.customUrl.url
                        },
                        method: 'POST',
                        url: '/api/menus/item/' + this.menu.id
                    }).then(function(response) {
                        var content = response.body;

                        if (content) {
                            this.menu.items.push({
                                depth: content.item.depth,
                                id: content.item.id,
                                isMinimized: true,
                                items: [],
                                key: content.item.order,
                                menuId: content.item.menu_id,
                                name: content.item.name,
                                order: content.item.order,
                                originalName: content.item.original_name,
                                parentId: content.item.parent_id,
                                status: content.item.status,
                                urlAnchor: content.item.url_anchor,
                                urlTarget: content.item.url_target,
                                urlType: content.item.url_type,
                                urlValue: content.item.url_value
                            });
                        }

                        this.customUrl = {
                            text: '',
                            url: ''
                        };

                        this.menuLoader = false;
                    }, function(response) {
                        vueStore.updateFormErrors(response.body);

                        if (response.body.alerts) {
                            response.body.alerts.forEach(function(alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }

                        this.menuLoader = false;
                    });
                }
            },
            confirmRename: function() {
                if (this.menu.name != this.newMenuName) {
                    this.$http({
                        body: {
                            name: this.newMenuName
                        },
                        method: 'PATCH',
                        url: '/api/menus/' + this.menu.id
                    }).then(function (response) {
                        if (response.body.alerts) {
                            response.body.alerts.forEach(function (alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }

                        this.menu.name = this.newMenuName;
                        this.menuRename = false;

                        this.menuLoader = false;
                    }, function (response) {
                        vueStore.updateFormErrors(response.body);

                        if (response.body.alerts) {
                            response.body.alerts.forEach(function (alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }
                        else {
                            vm.$emit('add-alert', {
                                type: 'error',
                                title: 'AJAX error',
                                message: response.statusText + ' (' + response.status + ')'
                            });
                        }

                        this.menuLoader = false;
                    });
                } else
                    this.menuRename = false;
            },
            deleteMenu: function() {
                vm.$emit('show-loader');

                this.$http.delete('/api/menus/' + this.menu.id).then(function(response) {
                    vm.$emit('hide-loader');

                    // redirect page
                    sessionStorage.setItem('alerts', JSON.stringify(response.body.alerts));

                    window.location = response.body.redirect;
                }, function(response) {
                    vm.$emit('hide-loader');

                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }
                    else {
                        vm.$emit('add-alert', {
                            type: 'error',
                            title: 'AJAX error',
                            message: response.statusText + ' (' + response.status + ')'
                        });
                    }
                });
            },
            deleteMenuItem: function(item) {
                if (!this.menu.items)
                    return;

                this.menuLoader = true;

                this.$http.delete('/api/menus/item/' + this.menu.id + '/' + item.id).then(function() {
                    this.menuLoader = false;

                    this.getData();
                }, function() {
                    this.menuLoader = false;

                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }
                    else {
                        vm.$emit('add-alert', {
                            type: 'error',
                            title: 'AJAX error',
                            message: response.statusText + ' (' + response.status + ')'
                        });
                    }
                });
            },
            getData: function() {
                if (!this.menuId)
                    return;

                this.contentTypesLoader = true;
                this.menuLoader = true;

                this.menu = '';

                // get content types
                this.$http.get('/api/content/types').then(function(response) {
                    var content = response.body;

                    if (content) {
                        this.contentTypes = content.data;

                        this.contentTypesLoader = false;
                    }
                }, function(response) {
                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }
                    else {
                        vm.$emit('add-alert', {
                            type: 'error',
                            title: 'AJAX error',
                            message: response.statusText + ' (' + response.status + ')'
                        });
                    }
                });

                var getMenuEndpoint = '/api/menus/' + this.menuId;
                if (this.withItems)
                    getMenuEndpoint += '?load=items';

                this.$http.get(getMenuEndpoint).then(function(response) {
                    var content = response.body;

                    if (content) {
                        this.menu = content;

                        this.initItems(this.menu.items);

                        this.newMenuName = this.menu.name;

                        this.menuLoader = false;
                    }
                }, function(response) {
                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }
                    else {
                        vm.$emit('add-alert', {
                            type: 'error',
                            title: 'AJAX error',
                            message: response.statusText + ' (' + response.status + ')'
                        });
                    }

                    this.menuLoader = false;
                });
            },
            getLanguages: function() {
                this.$http.get('/api/settings/languages').then(function(response) {
                    this.languages = response.body.data;
                }, function(response) {
                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }
                    else {
                        vm.$emit('add-alert', {
                            type: 'error',
                            title: 'AJAX error',
                            message: response.statusText + ' (' + response.status + ')'
                        });
                    }
                });
            },
            initItems: function(items) {
                if (items && items.length > 0) {
                    items.forEach(function (item) {
                        item.key = item.order;
                        item.menuId = item.menu_id;
                        item.originalName = item.original_name;
                        item.parentId = item.parent_id;
                        item.uid = str_random(18);
                        item.urlAnchor = item.url_anchor;
                        item.urlTarget = item.url_target;
                        item.urlType = item.url_type;
                        item.urlValue = item.url_value;
                        Vue.set(item, 'isMinimized', true);

                        this.initItems(item.items);
                    }.bind(this));
                }
            },
            reorderElements: function(dropElement, offset) {
                if (!this.menu.items)
                    return;

                if (dropElement !== this.dragElement) {
                    this.menuLoader = true;

                    var endpoint = offset > 30
                            ? '/api/menus/item/' + this.menu.id + '/' + dropElement.id + '/' + this.dragElement.id + '/indent'
                            : '/api/menus/item/' + this.menu.id + '/' + dropElement.id + '/' + this.dragElement.id + '/insert';

                    this.$http.patch(endpoint).then(function () {
                        this.menuLoader = false;

                        this.getData();
                    }, function () {
                        this.menuLoader = false;

                        if (response.body.alerts) {
                            response.body.alerts.forEach(function (alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }
                        else {
                            vm.$emit('add-alert', {
                                type: 'error',
                                title: 'AJAX error',
                                message: response.statusText + ' (' + response.status + ')'
                            });
                        }
                    });
                }

                this.dragElement = null;
            },
            saveForm: function(event) {
                vm.$emit('show-loader');

                var form = event.target;

                var action = form.getAttribute('action');
                var data = new FormData(form);
                var method = form.getAttribute('method').toUpperCase();

                this.$http({
                    body: data,
                    method: method,
                    url: action
                }).then(function(response) {
                    vueStore.updateFormErrors([]);

                    // redirect or refresh page
                    sessionStorage.setItem('alerts', JSON.stringify(response.body.alerts));

                    if (response.body.redirect && response.body.redirect != '')
                        window.location = response.body.redirect;
                    else
                        window.location.reload();
                }, function(response) {
                    vueStore.updateFormErrors(response.body);

                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }

                    vm.$emit('hide-loader');
                });

            },
            searchContent: debounce(function(typeId, event) {
                var term = event.target.value;

                if (term.length < 2)
                    return;

                this.contentLoader = true;
                this.searchResults[typeId] = [];

                // get content types
                this.$http.get('/api/content/manage/' + typeId, {
                    params: {
                        filters: {
                            language: this.menu.language,
                            search: term
                        },
                        perPage: 0
                    }
                }).then(function(response) {
                    var content = response.body;

                    if (content) {
                        content.data.forEach(function(item) {
                            this.searchResults[typeId][item.id] = item;
                        }.bind(this));

                        this.contentLoader = false;
                    }
                }, function(response) {
                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }
                    else {
                        vm.$emit('add-alert', {
                            type: 'error',
                            title: 'AJAX error',
                            message: response.statusText + ' (' + response.status + ')'
                        });
                    }
                });
            }, 500),
            setDragElement: function(dragElement) {
                if (!this.menu.items)
                    return;

                this.dragElement = dragElement;
            },
            toggleMenuRename: function() {
                this.menuRename = !this.menuRename;
            },
            toggleTab: function(typeId) {
                this.searchResults = [];
                this.searchTerm = '';
                this.selectedContent = [];

                if (this.openTab !== typeId)
                    this.openTab = typeId;
                else
                    this.openTab = null;
            }
        },
        props: {
            withItems: {
                default: false,
                type: Boolean
            },
            menuId: {
                default: null,
                type: Number
            }
        }
    });
</script>