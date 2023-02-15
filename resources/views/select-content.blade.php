<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel LTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<style>
    ol{
        list-style: none;
    }
</style>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Content Selection</h3>
                    </div>
                    <div class="card-body">
                        <p>Now choose the content you wish to display to your user</p>
                        <form action="{{ route('content-selected') }}" method="post">
                            <ol>
                                @for($i = 1; $i <= 5; $i++)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                        value="{{ $i }}" type="radio" name="content" id="flexRadioDefault2"
                                        @if($i === 1) checked @endif>
                                        <label class="form-check-label" for="content">
                                            content number #{{ $i }}
                                        </label>
                                    </div>
                                </li>
                                @endfor
                            </ol>
                            <input type="submit" value="Submit" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>