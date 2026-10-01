@extends('adminlte::page')

@section('title', '機材追加／削除 | 予約No. '.$order->order_no)

@section('css')
<link href="{{ asset('/css/style.css') }}" rel="stylesheet" type="text/css">
<style>
    #admin-machine-manage .table th,
    #admin-machine-manage .table td {
        padding-top: 0.1em;
        padding-bottom: 0.1em;
    }
    #admin-machine-manage .machine-table {
        table-layout: fixed;
    }
    #admin-machine-manage .machine-table th:first-child,
    #admin-machine-manage .machine-table td:first-child {
        width: 3.25rem;
        padding-left: 0;
        padding-right: 0;
        text-align: center;
        vertical-align: middle;
    }
    #admin-machine-manage .machine-table th:first-child {
        font-size: 0.875rem;
    }
    #admin-machine-manage tr.midashi > th {
        padding-left: 0.75em;
    }
    #admin-machine-manage .machine-table th:nth-child(2),
    #admin-machine-manage .machine-table td:nth-child(2) {
        width: 3rem;
        padding-left: 0;
        padding-right: 0;
        text-align: center;
    }
    #admin-machine-manage .machine-table th:nth-child(3),
    #admin-machine-manage .machine-table td:nth-child(3) {
        width: 35%;
    }
</style>
@endsection

@section('content')
    <h1 class="text-center p-2">@yield('title')</h1>

    <div id="admin-machine-manage" class="container box1000">
        @if(session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        <div class="darkgray p-2 mb-3 text-center">
            <div><strong>{{ $order->seminar_name }}</strong></div>
            <div>セミナー開催日: {{ $order->seminar_day }}</div>
            <div>予約期間: {{ $order->order_use_from }} ～ {{ $order->order_use_to }}</div>
        </div>

        <h4 class="text-bold text-center">追加可能な機材</h4>
        <form action="{{ route('admin.orders.machines.manage.update', $order->order_id) }}" method="post">
            @csrf
            <input type="hidden" name="operation" value="add">
            @error('add_machine_ids')
                <div class="text-danger mb-2">{{ $message }}</div>
            @enderror
            @error('add_machine_ids.*')
                <div class="text-danger mb-2">{{ $message }}</div>
            @enderror
            <div class="table-responsive">
                <table class="table table-striped machine-table">
                    <thead>
                        <tr class="midashi">
                            <th>追加</th>
                            <th>ID</th>
                            <th>機材番号</th>
                            <th>規格</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($availableMachines as $machine)
                            <tr>
                                <td><input type="checkbox" name="add_machine_ids[]" value="{{ $machine->machine_id }}" {{ in_array($machine->machine_id, old('add_machine_ids', [])) ? 'checked' : '' }}></td>
                                <td>{{ $machine->machine_id }}</td>
                                <td><a href="{{ route('pctool.detail', $machine->machine_id) }}" target="_blank">{{ $machine->machine_name }}</a></td>
                                <td>{{ $machine->machine_spec }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">追加可能な機材はありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-center mb-4">
                <button type="button" class="btn btn-primary m-2 p-1" data-toggle="modal" data-target="#confirmMachineAdd" {{ $availableMachines->isEmpty() ? 'disabled' : '' }}>選択した機材を追加</button>
            </div>
            <div class="modal fade" id="confirmMachineAdd" tabindex="-1" role="dialog" aria-labelledby="confirmMachineAddTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmMachineAddTitle">機材追加の確認</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="閉じる"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">選択した機材を予約に追加します。よろしいですか？</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                            <button type="submit" class="btn btn-primary">追加する</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <h4 class="text-bold text-center">削除対象の機材</h4>
        <form action="{{ route('admin.orders.machines.manage.update', $order->order_id) }}" method="post">
            @csrf
            <input type="hidden" name="operation" value="delete">
            @error('delete_machine_ids')
                <div class="text-danger mb-2">{{ $message }}</div>
            @enderror
            @error('delete_machine_ids.*')
                <div class="text-danger mb-2">{{ $message }}</div>
            @enderror
            <div class="table-responsive">
                <table class="table table-striped machine-table">
                    <thead>
                        <tr class="midashi">
                            <th>削除</th>
                            <th>ID</th>
                            <th>機材番号</th>
                            <th>規格</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selectedMachines as $machine)
                            <tr>
                                <td><input type="checkbox" name="delete_machine_ids[]" value="{{ $machine->machine_id }}" {{ in_array($machine->machine_id, old('delete_machine_ids', [])) ? 'checked' : '' }}></td>
                                <td>{{ $machine->machine_id }}</td>
                                <td><a href="{{ route('pctool.detail', $machine->machine_id) }}" target="_blank">{{ $machine->machine_name }}</a></td>
                                <td>{{ $machine->machine_spec }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">この予約に登録された機材はありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-danger m-2 p-1" data-toggle="modal" data-target="#confirmMachineDelete" {{ $selectedMachines->isEmpty() ? 'disabled' : '' }}>選択した機材を削除</button>
                <a class="btn btn-secondary m-2 p-1" href="{{ route('admin.orders.machines.edit', $order->order_id) }}">予約詳細に戻る</a>
            </div>
            <div class="modal fade" id="confirmMachineDelete" tabindex="-1" role="dialog" aria-labelledby="confirmMachineDeleteTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmMachineDeleteTitle">機材削除の確認</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="閉じる"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">選択した機材を予約から削除します。この操作は元に戻せません。続けますか？</div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                            <button type="submit" class="btn btn-danger">削除する</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
