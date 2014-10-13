
<!-- Main Content -->
<form action="{{ $list_action }}" method="get" class="tblist-form" autocomplete="off" id="users_tblist_form">
    <div class="row form-inline tblist-form-toolbar" >
        <div class="col-sm-10">
            <!-- Buck Actions -->
            <div class="form-group">
                <label class="sr-only" for="action_bulk">{{ lang('texts.users.status_placeholder') }}</label>
                <select name="status" class="form-control">
                    <option value="">{{ lang('texts.users.status_placeholder') }}</option>
                    @foreach(User::getUserStatuses() as $key=>$value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="sr-only" for="input_email">{{ lang('texts.users.email') }}</label>
                <input type="text" name="email" class="form-control" id="input_email" placeholder="{{ lang('texts.users.email_placeholder') }}" value="{{ Input::get('email') }}">
            </div>
        </div>
        <div class="col-sm-2 text-right">
            <button type="reset" class="btn btn-info"><i class="fa fa-times"></i> {{ lang('texts.reset_button') }}</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> {{ lang('texts.filter_button') }}</button>
        </div>
    </div><!-- /.row -->

    @include('_partials._tblist')
</form>