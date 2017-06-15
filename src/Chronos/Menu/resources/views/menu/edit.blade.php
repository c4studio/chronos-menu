
@extends('chronos::menu')

@section('content')
<div class="content-wrapper">
    <div class="content">
        <header class="subheader">
            <h1>{!! trans('chronos.menu::interface.Menus') !!}</h1>
            <ul class="breadcrumbs">
                <li><span class="icon c4icon-grid-2"></span></li>
                <li class="active">{!! trans('chronos.menu::interface.Menus') !!}</li>
            </ul>
            @if (count($menus) > 0)
                <ul class="nav nav-tabs">
                @foreach ($menus as $one_menu)
                    <li @if ($menu->id == $one_menu->id)class="active"@endif><a href="{{ route('chronos.menu.edit', ['menu' => $one_menu]) }}">{{ $one_menu->name }} @if (settings('is_multilanguage')) ({{ $one_menu->language }}) @endif</a></li>
                @endforeach
                </ul>
            @endif
            @can ('manage_newsletter_lists')
            <div class="main-action create">
                <a data-toggle="modal" data-target="#create-menu-dialog" data-placement="left" data-tooltip="tooltip" title="{!! trans('chronos.menu::interface.Create new menu') !!}" data-toggle="dropdown">{!! trans('chronos.menu::interface.Create new menu') !!}</a>
            </div>
            @endcan
        </header><!--/.subheader -->

        <menu-editor v-bind:menu-id="{{ $menu->id }}" v-bind:with-items="true" inline-template>
            <div id="menu-editor-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="panel-group" v-show="!contentTypesLoader">
                                <div class="panel" v-for="type in contentTypes">
                                    <div class="panel-heading" v-bind:class="{ active : openTab == type.id }" v-on:click="toggleTab(type.id)">
                                        <h2 class="panel-title"><a v-html="type.name"></a></h2>
                                    </div>
                                    <div class="panel-collapse collapse" v-bind:class="{ in : openTab == type.id }">
                                        <div class="panel-body">
                                            <input class="form-control" placeholder="{!! trans('chronos.menu::interface.Search') !!}" type="text" v-model="searchTerm" v-on:keyup="searchContent(type.id, $event)" />

                                            <div class="marginT15" v-show="!contentLoader && searchResults[type.id] && searchResults[type.id].length > 0">
                                                <div class="checkbox" v-for="content in searchResults[type.id]" v-if="typeof content !== 'undefined'">
                                                    <label><input v-bind:value="content.id" type="checkbox" v-bind:value="type.id" v-model="selectedContent" /> <span v-html="content.title"></span></label>
                                                </div>
                                                <a class="btn btn-action" v-on:click="addToMenu(type.id)">{!! trans('chronos.menu::forms.Add to menu') !!}</a>
                                            </div>

                                            <p class="text-center" v-show="contentLoader"><span class="loader-small"></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-heading" v-bind:class="{ active : openTab == 0 }" v-on:click="toggleTab(0)">
                                        <h2 class="panel-title"><a>{!! trans('chronos.menu::interface.Custom link') !!}</a></h2>
                                    </div>
                                    <div class="panel-collapse collapse" v-bind:class="{ in : openTab == 0 }">
                                        <div class="panel-body">
                                            <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'url_value') }">
                                                <label for="custom-url">{!! trans('chronos.menu::forms.URL') !!}</label>
                                                <input class="form-control" id="custom-url" type="text" v-model="customUrl.url" />
                                                <span class="help-block" v-html="store.formErrors['url_value'][0]" v-if="Object.hasKey(store.formErrors, 'url_value')"></span>
                                            </div>
                                            <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'name') }">
                                                <label for="custom-url-text">{!! trans('chronos.menu::forms.Link text') !!}</label>
                                                <input class="form-control" id="custom-url-text" type="text" v-model="customUrl.text" />
                                                <span class="help-block" v-html="store.formErrors['name'][0]" v-if="Object.hasKey(store.formErrors, 'name')"></span>
                                            </div>

                                            <a class="btn btn-action" v-on:click="addToMenu(0)">{!! trans('chronos.menu::forms.Add to menu') !!}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="text-center" v-show="contentTypesLoader"><span class="loader-small"></span></p>

                            @if (settings('is_multilanguage'))
                                <div class="panel">
                                    <h2 class="panel-title">{{ trans('chronos.menu::interface.Languages') }}</h2>
                                    <p class="paddingB15"><strong>{!! trans('chronos.menu::interface.The language of this menu is <em>:language</em>.', ['language' => $menu->languageName]) !!}</strong></p>
                                    <table class="table table-condensed">
                                        @foreach($languages as $language)
                                            @if ($menu->language != $language->code)
                                                <tr>
                                                    <td>{{ $language->name }}</td>
                                                    <td class="text-right">
                                                        @if ($menu->translation_codes->search($language->code) !== false)
                                                            <a class="icon c4icon-pencil-3 c4icon-lg" href="{{ $menu->admin_urls['translations'][$language->code] }}"></a>
                                                        @else
                                                            <a class="icon c4icon-plus-2 c4icon-lg" href="{{ $menu->endpoints['translate'] }}?language={{ $language->code }}"></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="col-xs-12 col-md-8">
                            <div class="panel" v-show="!menuLoader">
                                    <input name="_method" type="hidden" value="PATCH" />
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}" />

                                    <div class="menu-title">
                                        <h2 v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'name') }" v-show="!menuRename">
                                            <span v-html="menu.name" v-show="menu.name"></span>
                                            <em v-show="!menu.name">{!! trans('chronos.menu::forms.Unnamed menu') !!}</em>
                                        </h2>

                                        <input name="name" type="hidden" v-model="menu.name" v-if="!menuRename" />
                                        <input class="form-control" type="text" v-model="newMenuName" v-if="menuRename" />
                                        <a class="btn btn-primary rename-confirm" v-on:click="confirmRename" v-show="menuRename"><span class="c4icon c4icon-check-2"></span></a>
                                        <a class="btn btn-cancel rename-cancel" v-on:click="toggleMenuRename" v-show="menuRename"><span class="c4icon c4icon-cross-2"></span></a>

                                        <a class="rename-menu" v-on:click="toggleMenuRename" v-show="!menuRename">{!! trans('chronos.menu::interface.Rename') !!}</a>
                                        <a class="delete-menu" data-toggle="modal" data-target="#delete-menu-dialog" v-show="!menuRename">{!! trans('chronos.menu::interface.Delete menu') !!}</a>
                                    </div>

                                    <ul class="menu-item-list" v-if="menu.items && menu.items.length > 0">
                                        <one-menu-item v-for="(item, key) in menu.items" v-bind:item-data="item" v-bind:items="item.items" v-bind:key="item.key" v-bind:menu-order="item.order"></one-menu-item>
                                    </ul>

                                    <p class="no-results" v-if="!menu.items || menu.items.length == 0">{!! trans('chronos.menu::interface.Add items to the menu from the left panel.') !!}</p>
                            </div>

                            <p class="text-center" v-show="menuLoader"><span class="loader-small"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </menu-editor>
    </div><!--/.content -->
</div><!--/.content-wrapper -->
@endsection

@push('content-modals')
<div class="modal fade" id="create-menu-dialog" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <menu-editor inline-template>
                <form action="{{ route('api.menu.store', ['menu' => $menu]) }}" method="POST" v-on:submit.prevent="saveForm" novalidate="novalidate">
                    <input name="_token" type="hidden" value="{{ csrf_token() }}" />

                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.menu::interface.Create new menu') !!}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'name') }">
                            <label class="control-label label-req" for="name">{!! trans('chronos.menu::forms.Name') !!}</label>
                            <input class="form-control" id="name" name="name" src="{{ route('api.settings.languages.all') }}">
                            <span class="help-block" v-html="store.formErrors['name'][0]" v-if="Object.hasKey(store.formErrors, 'name')"></span>
                        </div>
                        @if (settings('is_multilanguage'))
                            <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'language') }">
                                <label class="control-label label-req" for="name">{!! trans('chronos.menu::forms.Language') !!}</label>
                                <select class="form-control" id="language" name="language">
                                    <option v-for="language in languages" v-bind:value="language.code" v-html="language.name"></option>
                                </select>
                                <span class="help-block" v-html="store.formErrors['language'][0]" v-if="Object.hasKey(store.formErrors, 'language')"></span>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.menu::interface.Close') !!}</button>
                        <button class="btn btn-primary" name="process" type="submit" value="1">{!! trans('chronos.menu::forms.Save') !!}</button>
                    </div>
                </form>
            </menu-editor>
        </div>
    </div>
</div>
<div class="modal fade" id="delete-menu-dialog" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-danger">
            <menu-editor v-bind:menu-id="{{ $menu->id }}" inline-template>
                <form v-on:submit.prevent="deleteMenu">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.menu::interface.Delete menu') !!}</h4>
                    </div>
                    <div class="modal-body">
                        <p class="marginT15 text-center"><strong>{!! trans('chronos.menu::interface.WARNING! This action is irreversible.') !!}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.menu::interface.Close') !!}</button>
                        <button class="btn btn-danger" name="process" type="submit" value="1">{!! trans('chronos.menu::interface.Delete') !!}</button>
                    </div>
                </form>
            </menu-editor>
        </div>
    </div>
</div>
@endpush



@push('scripts-components')
    @include('chronos::components.menu_editor')
@endpush