<?php
/**
 * Created by PhpStorm.
 * User: DanielSimangunsong
 * Date: 2/14/2017
 * Time: 5:58 PM
 */ ?>
@if ([] !== $assigns  || $admin->isDaemon())
    @push('view-style')
    <style type="text/css">
        table#permissions {
            width: 100%;
            border-spacing: 1px;
            border-collapse: separate;
        }

        table#permissions thead tr {
            background-color: #1a2226;
            color: #fff;
        }

        table#permissions th, td {
            padding: 5px;
        }

        table#permissions tr.module {
            background-color: #cccccc;
        }

        table#permissions tr.panel.even {
            background-color: #eeeeee;
        }

        table#permissions tr.panel td.active {
            background-color: #EFEFEF;
        }

        table#permissions td:nth-child(1) {
            text-align: left;
        }

        table#permissions td {
            text-align: center;
        }
    </style>
    @endpush

    {{ Form::open() }}
    <table id="permissions">
        @set(headers, [])
        @foreach (Wa::modules() as $module)
            @if ([] !== (Wa::module($module)->getPanels()))
                @foreach (Wa::module($module)->getPanels() as $panel => $info)
                    @if ([] !== $info->getActions())
                        @foreach ($info->getActions() as $action => $setting)
                            @if (1 === array_get($assigns, $module . '.' . $panel . '.' . $action) || $admin->isDaemon())
                                @set(headers[$action], Wa::trans($action))
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ([] !== $headers)
            {? ksort($headers) ?}
            @set(headersCount, count($headers))
            <thead>
            <tr>
                <th colspan="2">Module</th>
                <th colspan="{{ $headersCount }}">Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach (Wa::modules() as $module)
                @if (null !== (array_get($assigns, $module)) || $admin->isDaemon())
                    @if ([] !== (Wa::module($module)->getPanels()))
                        @set(panelIncrement, 1)
                        @set(printHead, true)
                        @foreach (Wa::module($module)->getPanels() as $panel => $info)
                            @if ((null !== (array_get($assigns, $module . '.' . $panel)) || $admin->isDaemon()))
                                @if (true === $printHead)
                                    <tr class="module">
                                        <td>{{Wa::trans($module)}}</td>
                                        <td>Toggle</td>
                                        @foreach ($headers as $action)
                                            <td> {{ $action  }}</td>
                                        @endforeach
                                    </tr>
                                    @set(printHead, false)
                                @endif

                                @if ([] !== $info->getActions())
                                    <tr class="panel {{0 === $panelIncrement % 2 ? 'even' : 'odd' }}">
                                        <td>{{ Wa::trans($info->getTitle()) }}</td>
                                        <td class="toggle"> {!! Form::checkbox('toggle') !!}</td>
                                        @set(headerIncrement, 0)
                                        @foreach ($headers as $action => $label)
                                            <td class="panel-{{ $action }}">
                                                @if (null !== $info->getAction($action)
                                                        && (1 === array_get($assigns, $module . '.' . $panel . '.' . $action) || $admin->isDaemon()))
                                                    {!!

                                                        Form::checkbox( $module . '-' . $panel . '-' . $action, 1,
                                                            null !== array_get($permissions, $module . '.' . $panel . '.' . $action),
                                                            [ 'name' => $module . "[$panel][$action]" ]
                                                        )
                                                   !!}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @increment(panelIncrement)
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endif
            @endforeach
            </tbody>
        @endif
        <tbody>
        <tr>
            <td style="padding: 30px 0px" colspan="2">
                {{ Form::hidden('remote-value', Illuminate\Support\Arr::serialize($permissions)) }}
                {{ Form::submit('Update', ['class' => 'btn btn-primary']) }}
            </td>
        </tr>
        </tbody>
    </table>
    {{ Form::close() }}
@endif


@push('view-script')
<script type="text/javascript">
    $(function () {
        $('td.toggle input[type="checkbox"]').change(function () {
            if ($(this).prop('checked')) {
                $(this).parent('td').siblings('td').find('input').prop('checked', true);
            } else {
                $(this).parent('td').siblings('td').find('input').prop('checked', false);
            }
        });
    });
</script>
@endpush