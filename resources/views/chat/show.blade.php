{{--
    This view is handled by ConversationController::show()
    which returns chat.index with activeConversation set.
    This file exists as a fallback redirect only.
--}}
@extends('chat.layout')

@section('chat-content')
    <div class="flex-1 flex items-center justify-center">
        <p class="text-gray-400 text-sm">Redirecting...</p>
    </div>
@endsection