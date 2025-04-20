<!-- resources/views/products/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Daftar Produk {{ count($products) }}</h1>
        <div class="row">
            @foreach (session('products', []) as $product)
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product['name'] }}</h5>
                            <p class="card-text">Harga: Rp {{ number_format($product['price'], 2) }}</p>
                            <form action="{{ route('cart.add', $product['id']) }}" method="POST">
                                @csrf
                                <input type="text" value="{{ $product['name'] }}" name="{{ $product['name'] }}" hidden>
                                <input type="text" value="{{ $product['price'] }}" name="{{ $product['price'] }}" hidden>

                                <button type="submit" class="btn btn-primary">Tambah ke Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <a href="{{ route('welcome-cart') }}" class="btn btn-primary">Lihat Cart</a>
    </div>
</body>
</html>
