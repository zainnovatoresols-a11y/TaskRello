@extends('layouts.app')

@section('title', $card->title)

@section('content')
    @include('cards.show')
@endsection

@section('scripts')
    <script src="{{ asset('js/board.js') }}"></script>
@endsection
