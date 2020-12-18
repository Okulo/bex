@extends('adminlte::page')

@section('content_header')
<x-week-header header="Косметологи"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Комиссии косметологов за неделю
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <form action="{{ route('cosmetologists.update.comissions') }}" method="post">
                        @csrf
                        @method("PUT")
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>
                                    Косметолог
                                </th>
                                <th>
                                    Комиссия
                                </th>
                            </thead>
                            <tbody>
                                @foreach($cosmetologists as $cosmetologist)
                                <tr>
                                    <td class="align-middle">
                                        {{ $cosmetologist->name }}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="comissions[{{$cosmetologist->id}}]" placeholder="Комиссия в тенге" required value="{{ $cosmetologist->getComission(week()->start(), week()->end()) }}" />
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="form-group text-right px-4">
                            <input type="hidden" name="startDate" value="{{ week()->start() }}" />
                            <input type="hidden" name="endDate" value="{{ week()->end() }}" />
                            <button type="submit" class="btn btn-warning btn-sm">
                                Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Внимание!</h4>
            <p>
                После того, как сохраните данные, обязательно пересчитайте бюджеты за всю неделю!
            </p>
            <hr>
            <p class="mb-0">
                Ссылка на страницу: <a href="{{ route('managers.weekplan') }}" class="text-dark">Недельный план</a>
            </p>
        </div>
    </div>
</div>
@stop
