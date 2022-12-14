<!-- TOP MENU BAR -->
<div class="app-header header">
    <div class="container-fluid"> 
        <div class="d-flex">
            <a class="header-brand" href="{{ url('/') }}">
                <img src="{{URL::asset('img/brand/logo.png')}}" class="header-brand-img desktop-lgo" alt="Polly logo">
                <img src="{{URL::asset('img/brand/favicon.png')}}" class="header-brand-img mobile-logo" alt="Polly logo">
            </a>
            <div class="app-sidebar__toggle nav-link icon" data-toggle="sidebar">
                <a class="open-toggle" href="{{url('#')}}">
                    <span class="fa fa-align-left header-icon"></span>
                </a>
            </div>
            <!-- SEARCH BAR -->
            <div id="search-bar">                
                <div>
                    <a class="nav-link icon">
                        <form id="search-field" action="{{ route('search') }}" method="POST" enctype="multipart/form-data">         
                            @csrf                   
                            <input type="search" name='keyword'>
                        </form>                        
                    </a>
                </div>                
            </div>
            <!-- END SEARCH BAR -->
            <!-- MENU BAR -->
            <div class="d-flex order-lg-2 ml-auto"> 
                <div class="dropdown header-notify">
                    <a class="nav-link icon" data-toggle="dropdown">                        
                        @role('admin')
                            <span class="header-icon fa fa-bell-o pr-3"></span>
                            @if (auth()->user()->unreadNotifications->where('type', '<>', 'App\Notifications\GeneralNotification')->count())
                                <span class="pulse "></span>
                            @endif
                        @endrole
                        @role('user|subscriber')
                            @if (config('settings.user_notification') == 'enabled')
                                <span class="header-icon fa fa-bell-o pr-3"></span>                            
                                    @if (auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification')->count())
                                        <span class="pulse "></span>
                                    @endif                            
                            @endif
                        @endrole
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow  animated">
                        @role('admin')
                            @if (auth()->user()->unreadNotifications->where('type', '<>', 'App\Notifications\GeneralNotification')->count())
                                <div class="dropdown-header">
                                    <h6 class="mb-0 fs-12 font-weight-bold"><span id="total-notifications"></span> <span class="text-primary">{{ __('New') }}</span> {{ __('Notification(s)') }}</h6>
                                    <a href="#" class="mb-1 badge badge-primary ml-auto pl-3 pr-3 mark-read" id="mark-all">{{ __('Mark All Read') }}</a>
                                </div>
                                <div class="notify-menu">
                                    <div class="notify-menu-inner">
                                        @foreach ( auth()->user()->unreadNotifications->where('type', '<>', 'App\Notifications\GeneralNotification') as $notification )
                                            <div class="d-flex dropdown-item border-bottom pl-4 pr-4">
                                                @if ($notification->data['type'] == 'new-user')                                                
                                                    <div>
                                                        <a href="{{ route('admin.notifications.systemShow', [$notification->id]) }}" class="d-flex">
                                                            <div class="notifyimg bg-info-transparent text-info"> <i class="mdi mdi-account-plus fs-18"></i></div>
                                                            <div class="mr-6">
                                                                <div class="font-weight-bold fs-12">{{ __('New User Registered') }}</div>
                                                                <div class="text-muted fs-10">{{ __('Name') }}: {{ $notification->data['name'] }}</div>
                                                                <div class="small text-muted fs-10">{{ $notification->created_at->diffForHumans() }}</div>
                                                            </div>                                            
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <a href="#" class="badge badge-primary mark-read mark-as-read" data-id="{{ $notification->id }}">{{ __('Mark as Read') }}</a>
                                                    </div>
                                                @endif                                                
                                            </div>
                                        @endforeach  
                                    </div>                              
                                </div>
                                <div class="view-all-button text-center">                            
                                    <a href="{{ route('admin.notifications.system') }}" class="fs-12 font-weight-bold">{{ __('View All Notifications') }}</a>
                                </div>                            
                            @else
                                <div class="view-all-button text-center">
                                    <h6 class=" fs-12 font-weight-bold mb-1">{{ __('There are no new notifications') }}</h6>                                    
                                </div>
                            @endif
                        @endrole
                        @if (config('settings.user_notification') == 'enabled')
                            @role('user|subscriber')
                                @if (auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification')->count())
                                    <div class="dropdown-header">
                                        <h6 class="mb-0 fs-12 font-weight-bold">{{ auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification')->count() }} <span class="text-primary">New</span> Notification(s)</h6>
                                        <a href="#" class="mb-1 badge badge-primary ml-auto pl-3 pr-3 mark-read" id="mark-all">{{ __('Mark All Read') }}</a>
                                    </div>
                                    <div class="notify-menu">
                                        <div class="notify-menu-inner">
                                            @foreach ( auth()->user()->unreadNotifications->where('type', 'App\Notifications\GeneralNotification') as $notification )
                                                <div class="dropdown-item border-bottom pl-4 pr-4">
                                                    <div>
                                                        <a href="{{ route('user.notifications.show', [$notification->id]) }}" class="d-flex">
                                                            <div class="notifyimg bg-info-transparent text-info"> <i class="fa fa-bell fs-18"></i></div>
                                                            <div>
                                                                <div class="font-weight-bold fs-12 mt-2">{{ __('New') }} {{ $notification->data['type'] }} {{ __('Notification') }}</div>
                                                                <div class="small text-muted fs-10">{{ $notification->created_at->diffForHumans() }}</div>
                                                            </div>                                            
                                                        </a>
                                                    </div>                                            
                                                </div>
                                            @endforeach                                
                                        </div>
                                    </div>
                                    <div class="view-all-button text-center">                            
                                        <a href="{{ route('user.notifications') }}" class="fs-12 font-weight-bold">{{ __('View All Notifications') }}</a>
                                    </div>                             
                                @else
                                    <div class="view-all-button text-center">
                                        <h6 class=" fs-12 font-weight-bold mb-1">{{ __('There are no new notifications') }}</h6>                                    
                                    </div>
                                @endif
                            @endrole
                        @endif                        
                    </div>
                </div>
                <div class="dropdown header-expand" >
                    <a  class="nav-link icon" id="fullscreen-button">
                        <span class="header-icon  mdi mdi-arrow-expand-all" id="fullscreen-icon"></span>
                    </a>
                </div>
                <div class="dropdown header-locale">
                    <a class="nav-link icon" data-toggle="dropdown">
                        <span class="header-icon fa fa-globe pr-1"></span><span class="text-brown fs-12 pr-5">{{ Config::get('locale')[App::getLocale()]['code'] }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow animated">
                        <div class="local-menu">
                            @foreach (Config::get('locale') as $lang => $language)
                                @if ($lang != App::getLocale())
                                    <a href="{{ route('locale', $lang) }}" class="dropdown-item d-flex pl-4">
                                        <div class="text-info"><i class="flag flag-{{ $language['flag'] }} mr-4"></i></div>
                                        <div>
                                            <span class="font-weight-normal fs-12">{{ $language['display'] }}</span>
                                        </div>
                                    </a>                                        
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>                
                <div class="dropdown profile-dropdown">
                    <a href="{{url('/' . $page='#')}}" class="nav-link" data-toggle="dropdown">
                        <span class="float-right">
                            <img src="@if(auth()->user()->profile_photo_path){{ asset(auth()->user()->profile_photo_path) }} @else {{ URL::asset('img/users/avatar.jpg') }} @endif" alt="img" class="avatar avatar-md">
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow animated">
                        <div class="text-center">
                            <span class="dropdown-item text-center user fs-12 pb-0 font-weight-bold">{{ Auth::user()->name }}</span>
                            <span class="text-center fs-12 text-muted">{{ Auth::user()->job_role }}</span>
                            <div class="dropdown-divider"></div>
                        </div>
                        <a class="dropdown-item d-flex" href="{{ route('user.subscriptions') }}">
                            <span class="profile-icon fa-solid fa-badge-dollar"></span>
                            <div class="fs-12">{{ __('Increase Balance') }}</div>
                        </a>
                        <a class="dropdown-item d-flex" href="{{ route('user.tts.results') }}">
                            <span class="profile-icon fa-solid fa-folder-music"></span>
                            <div class="fs-12">{{ __('TTS Results') }}</div>
                        </a>
                        <a class="dropdown-item d-flex" href="{{ route('user.projects') }}">
                            <span class="profile-icon fa-solid fa-boxes-packing"></span>
                            <div class="fs-12">{{ __('My TTS Projects') }}</div>
                        </a>
                        @if (config('tts.enable.sound_studio') == 'on')
                            <a class="dropdown-item d-flex" href="{{ route('user.studio') }}">
                                <span class="profile-icon fa-solid fa-photo-film-music"></span>
                                <div class="fs-12">{{ __('Sound Studio') }}</div>
                            </a>
                        @endif  
                        <a class="dropdown-item d-flex" href="{{ route('user.profile') }}">
                            <span class="profile-icon mdi mdi-account-edit"></span>
                            <div class="fs-12">{{ __('Profile') }}</div>
                        </a>
                        <a class="dropdown-item d-flex" href="{{ route('user.password') }}">
                            <span class="profile-icon mdi mdi-account-key"></span>
                            <div class="fs-12">{{ __('Change Password') }}</div>
                        </a>
                        <a class="dropdown-item d-flex" href="{{ route('logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"> 
                            <span class="profile-icon mdi mdi-upload-network"></span>          
                            <div class="fs-12">{{ __('Logout') }}</div>                            
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
            <!-- END MENU BAR -->
        </div>
    </div>
</div>
<!-- END TOP MENU BAR -->
