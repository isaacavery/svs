<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-theme.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript">
        var csrf_token = '{{ csrf_token() }}';
        var resource_id = '{{ (isset($sheet->id)) ? $sheet->id : ''}}';
    </script>
    <script src="{{ asset('js/svs.js') }}"></script>

</head>
<body style="padding-top:60px">
<style type="text/css">
    #messages {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999;
        width: 100%;
    }
    #blockui {
        width: 100%;
        height: calc(100% + 120px);
        background: rgba(0,0,0,0.5);
        position: absolute;
        z-index:1000;
        top: -120px;
        min-height: 300px;
        display: none;
    }
</style>
    <div id="blockui"></div>
    <div id="app">
        <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Petition Checker') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    @if(Auth::check())
                    <ul class="nav navbar-nav">
                    @if(Auth::user()->admin)
                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sheets <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/sheets/create">Upload</a></li>
                        </ul>
                        </li>
                    @endif
                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Circulators <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/circulators/queue">Queue</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Add</a></li>
                        </ul>
                        </li>
                        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Signers <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/sheets/queue">Queue</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Add</a></li>
                        </ul>
                        </li>
                    </ul>
                    @endif

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                    <li><a href="/users/{{Auth::user()->id}}">Profile</a>
                                    @if(Auth::user()->admin)
                                    <li><a href="{{ route('register') }}">Add User</a>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
        <div id="modalComment" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Please enter reason for flagging the sheet.</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <div class="form-group">  
                                {{ Form::textarea('comment','',['placeholder'=>'Describe the problem...', 'style' => 'width: 100%;','rows'=>3, 'id' => 'comment']) }}
                            </div> 
                        </form>  
                    </div> 
                    <div class="modal-footer">
                        <a href="#" type="button" class="btn btn-primary pull-right close" id="#flagBtn">Flag Sheet</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="ajaxSpinnerContainer">
    <img src="{{URL::asset('/img/ajax-loader.gif')}}" id="ajaxSpinnerImage" title="working...">
    </div>  
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
