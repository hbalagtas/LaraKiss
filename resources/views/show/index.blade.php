@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{{$show->name}}</div>

                <div class="panel-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th> 
                                <th>Priority</th>                                
                                <th>Downloaded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($show->episodes as $episode)
                            <tr>
                                <td>{{$episode->id}}</td>
                                <td><a href="{{$episode->url}}">{{$episode->name}}</a></td>  
                                <th>{{$episode->priority}}</th>                               
                                <td>
                                    @if ( $episode->downloaded)
                                        <i class="fa fa-cloud-download" aria-hidden="true"></i>
                                    @else
                                        <i class="fa fa-spinner" aria-hidden="true"></i>
                                    @endif                                
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
