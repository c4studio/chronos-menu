
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
                    <li><a href="{{ route('chronos.menu.edit', ['menu' => $one_menu]) }}">{{ $one_menu->name }} @if (settings('is_multilanguage')) ({{ $one_menu->language }}) @endif</a></li>
                @endforeach
                </ul>
            @endif
            @can ('manage_newsletter_lists')
            <div class="main-action create">
                <a data-toggle="modal" data-target="#create-menu-dialog" data-placement="left" data-tooltip="tooltip" title="{!! trans('chronos.menu::interface.Create new menu') !!}" data-toggle="dropdown">{!! trans('chronos.menu::interface.Create new menu') !!}</a>
            </div>
            @endcan
        </header><!--/.subheader -->

        @if (count($menus) > 0)
            <p class="no-results">{!! trans('chronos.menu::interface.Select a menu from the tabs above.') !!}</p>
        @else
            <p class="no-results">{!! trans('chronos.menu::interface.There are no menus yet. <a \:attributes>Create one now</a>.', ['attributes' => 'data-toggle="modal" data-target="#create-menu-dialog"']) !!}</p>
        @endif
    </div><!--/.content -->
</div><!--/.content-wrapper -->
@endsection

@push('content-modals')
<div class="modal fade" id="create-menu-dialog" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <menu-editor inline-template>
                <form action="{{ route('api.menu.store') }}" method="POST" v-on:submit.prevent="saveForm" novalidate="novalidate">
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
@endpush



@push('scripts-components')
    @include('chronos::components.menu_editor')
@endpush