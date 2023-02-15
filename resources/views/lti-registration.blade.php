<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel LTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h3>LTI Tool Information</h3>
                    </div>
                    <div class="card-body">
                        <p>First, copy these informations and paste to your platform when registering the tool</p>
                        <table class="table">
                            <tr>
                                <td>Tool name</td>
                                <td>:</td>
                                <td>{{ config('app.name') }}</td>
                            </tr>
                            <tr>
                                <td>Tool URL</td>
                                <td>:</td>
                                <td>{{ config('app.url') }}</td>
                            </tr>
                            <tr>
                                <td>Tool Keyset URL</td>
                                <td>:</td>
                                <td>{{ route('jwks') }}</td>
                            </tr>
                            <tr>
                                <td>OIDC Auth endpoint</td>
                                <td>:</td>
                                <td>{{ route('oidc') }}</td>
                            </tr>
                            <tr>
                                <td>Launch URI(s)</td>
                                <td>:</td>
                                <td>
                                    {{ route('launch') }} <br>
                                    {{ route('deeplinking') }}<br>
                                </td>
                            </tr>
                            <tr>
                                <td>Content-selection URL</td>
                                <td>:</td>
                                <td>
                                    {{ route('deeplinking') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h3>LTI Platform</h3>
                    </div>
                    <div class="card-body">
                        <p>Then copy informations you got from your platform</p>
                        @if ($message = Session::get('success'))
                        <div class="alert alert-primary alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            {{ $message }}
                        </div>
                        @endif
                        <form action="{{ route('register-platform') }}" method="post">
                            {{ csrf_field() }}
                            <div class="mb-3">
                                <label for="" class="form-label">
                                    Platform Name <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    required
                                    id=""
                                    value="{{ $platform ? $platform->name : ''}}"
                                    placeholder="">
                                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">
                                    Issuer/Platform ID <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="issuer"
                                    required
                                    id=""
                                    value="{{ $platform ? $platform->issuer : ''}}"
                                    aria-describedby="helpId" placeholder="">
                                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">
                                    Client ID <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                value="{{ $platform ? $platform->client_id : ''}}"
                                class="form-control" name="client_id" required id="" aria-describedby="helpId" placeholder="">
                                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">
                                    Public Keyset / JWKS URL <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                value="{{ $platform ? $platform->public_keyset_url : ''}}"
                                class="form-control" name="jwks_url" id="" aria-describedby="helpId" placeholder="">
                                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">
                                    OIDC Login endpoint <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                value="{{ $platform ? $platform->authentication_request_url : ''}}"
                                class="form-control" name="oidc_endpoint" required id="" aria-describedby="helpId" placeholder="">
                                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">access token URL</label>
                                <input type="text"
                                value="{{ $platform ? $platform->access_token_url : ''}}"
                                class="form-control" name="access_token_url" id="" aria-describedby="helpId" placeholder="">
                                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deployment ID</label>
                                <input
                                value="{{ $platform ? $platform->deployments[0]->deployment_id : ''}}"
                                type="text" class="form-control" name="deployment_id" id="" aria-describedby="helpId" placeholder="">
                                <!-- <small id="helpId" class="form-text text-muted">Help text</small> -->
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>