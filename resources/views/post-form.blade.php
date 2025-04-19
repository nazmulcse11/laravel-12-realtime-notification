<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">
    <title>Real Time Notification With Sound!</title>
  </head>
  <body>
    <div class="container shadow p-4 mb-5 bg-white rounded">
      <h2 class="mt-3 text-center">Real Time Notification With Sound!</h2>

      <nav class="navbar navbar-expand-lg bg-dark border-bottom border-body" data-bs-theme="dark">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">Home</a>
          <div class="collapse navbar-collapse" id="navbarText">

            <!-- Notification Icon -->
            @if (Auth::check())

            @php 
            $user_all_notification = App\Models\UserNotification::where('user_id',Auth::user()
            ->id)->where('is_read','unread')->latest()->get(); 
            @endphp
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                @csrf
                  <a class="nav-link active" 
                  aria-current="page" 
                  href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                  this.closest('form').submit();"
                  >
                  Logout
                </a>
                </form>
              </li>
              <li class="nav-item dropdown notification-area">
                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                  <span class="position-absolute translate-middle badge rounded-pill bg-danger">
                    {{ $user_all_notification->count() ?? 0 }}
                  </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  @foreach($user_all_notification as $notification)
                  <li><a class="dropdown-item" href="#">{{ $notification->message ?? '' }}</a></li>
                  @endforeach
                </ul>
              </li>
            </ul>
            @else
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="{{ route('login') }}">Login</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="{{ route('register') }}">Register</a>
              </li>
            </ul> 
            @endif

          </div>
        </div>
      </nav>

        <div class="row">
            <div class="col-lg-12">
            @if (!Auth::check())
                <form class="mt-5" action="{{ route('post.create')}}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Post Title</label>
                        <input type="text" class="form-control" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Post Description</label>
                        <textarea class="form-control" rows="3" name="description"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Post</button>
                </form>
                @else
                <div class="d-flex justify-content-between">
                  <h2 class="mt-3">My Dashboard</h2>
                  <button class="btn btn-warning mt-3" id="enable-notifications">Enable Sound Notifications</button>
                  @endif
              </div>
        </div>

      </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <script src="https://cdn.bootcss.com/jquery/3.7.1/jquery.min.js"></script>
      <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

      <script>

    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;

    @auth
    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
      cluster: '{{ env('PUSHER_APP_CLUSTER')}}',
      channelAuthorization: {
        endpoint: '{{ url("/broadcasting/auth") }}',
      }
    });

    var user_id = {{ Auth::id() }};
    var channel = pusher.subscribe('private-user-notification-' + user_id);
    channel.bind('App\\Events\\NotificationSent', function(data) {
      console.log(data);

      toastr.warning(data.message, 'New Notiofication')
      $('.notification-area').load(location.href + ' .notification-area');
      let notification_sound = new Audio('/notification-sound.mp3');
      notification_sound.play();
    });
    @endauth
  </script>


  </body>
</html>