<?php  
  
namespace App\Livewire\Pages\Visitor\Partials;  
  
use Livewire\Component;  
use App\Models\ProductBrand;  
use App\Models\ProductContent;  
use App\Models\ProductCategoryFirst;  
use App\Models\SalesCart;  
use App\Models\SalesCartDetail;  
use Illuminate\Support\Facades\Session;  
use App\Helpers\Cart\Cart; 
use Livewire\Attributes\Computed;

class Header extends Component  
{  
    public $brands = [];  
    public $cartItems ;  
    public $addToCart ;  
    public $removeCartItem ;  
    public $updateCartItem ;  
    public $isProductInCart ;  
    public $calculateTotal ;  
    public $calculateDiscount ;  
    public $calculateShipping ;  
    public $calculateFinalTotal ;  
    public $index = 0;  
    public $sessionId;  
    public $products = [];
    public $productContent = [];
    public $productCategoryFirsts = [];

    public $tes1 ;
    
  #[\Livewire\Attributes\Locked]
  protected $parentModel = \App\Models\ProductContent::class;

  #[\Livewire\Attributes\Locked]
  public array $options = [];


  #[\Livewire\Attributes\Locked]
  public null|string $id = null;

    public function rules()  
    {  
        return [  
            'cartItems.*.qty' => 'required|integer|min:1', 
        ];
    }  

    protected $listeners = ['refreshCart' => 'loadCartItems1'];


    public function mount()  
    {  
        $this->tes1 = ['tes1-1','tes-2','tes-3'];

        $cart = new Cart();
        $this->products = session('products', []);
        // $this->addToCart = $cart->addToCart();
        // $this->isProductInCart = $cart->isProductInCart();
        // $this->updateCartItem = $cart->updateCartItem();
        // $this->removeCartItem = $cart->removeCartItem();
        // $this->calculateTotal = $cart->calculateTotal();
        // $this->calculateDiscount = $cart->calculateDiscount();
        // $this->calculateShipping = $cart->calculateShipping();
        // $this->calculateFinalTotal = $cart->calculateFinalTotal();

        $this->initialize();
    }
    
   

    public function initialize()  
    {  
        $this->brands = ProductBrand::all();  
        $this->sessionId = Session::getId(); 
        // $this->productCategoryFirsts = ProductContent::with('product')->get(); 
        $this->productCategoryFirsts = ProductCategoryFirst::with('product')->get(); 
        $this->options['product_category_firsts'] = ProductCategoryFirst::all(); 
    }

    public function render()  
    {  
        return view('livewire.pages.visitor.partials.header');  
    }  

    
    public function addToCart($productId, $sellingPrice, $weight)  
    { 
        $sessionId = Session::getId(); 

        if (!DB::table('sessions')->where('id', $sessionId)->exists()) {  
            DB::table('sessions')->insert([  
                'id' => $sessionId,  
                'payload' => 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiNmR2WThQaWRFY1hDVTZDaTN4TjJQbjE1TFNVV2hQSHRBQkNKbENCSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',  
                'last_activity' => time(),  
            ]);  
        }  

        $cart = SalesCart::firstOrCreate(  
            ['session_id' => $sessionId],  
            ['date' => now()] 
        );

        // $this->product = Product::where('id',$sessionId);

        $cartDetail = SalesCartDetail::where([  
            'sales_cart_id' => $cart->id,  
            'product_id' => '9d921fc2-1ebf-46ba-a613-29cd580781e7',  
            'selling_price' => $sellingPrice,  
            'weight' => $weight,  
        ])->first();
  
        if ($cartDetail) {  
            // If the product is already in the cart, increase the quantity  
            $cartDetail->qty += 1;  
            $cartDetail->amount += $sellingPrice;  
            $cartDetail->subtotal_weight += $weight;  
        } else {  
            // If the product is not in the cart, create a new cart detail  
            $cartDetail = new SalesCartDetail([  
                'sales_cart_id' => $cart->id,  
                'product_id' => $productId,  
                'selling_price' => $sellingPrice,  
                'discount_persentage' => 0,  
                'discount_value' => 0,  
                'nett_price' => $sellingPrice,  
                'qty' => 1,  
                'amount' => $sellingPrice,  
                'weight' => $weight,  
                'subtotal_weight' => $weight,  
            ]);  
        }  

        // Save the cart detail  
        $cartDetail->save();  
        $this->dispatch('item-added', productId: $productId, sellingPrice:$sellingPrice, weight:$weight);

        $this->success('Data Updated');
        // session()->flash('message', 'Product added to cart successfully!');  
    } 

    public function loadCartItems1($cartDetail)  
    {
        dd('cek', $cartDetail); 
    }
    
    #[Computed]
    public function loadCartItems()  
    { 
        $cart = SalesCart::where('session_id', $this->sessionId)->first(); 
        if ($cart) {  
            $this->cartItems = $cart->details;
        } else {  
            $this->cartItems = [];
        }


        return $this->cartItems;
    } 

    public function isProductInCart($productId) 
    {  
        return SalesCartDetail::where('product_id', $productId)    
        ->exists(); 
    }

    public function updateCartItem($cartDetailId, $qty)  
    {  
        try {  
            $cartDetail = SalesCartDetail::find($cartDetailId);  
            if ($cartDetail) {  
                $cartDetail->qty = $qty;  
                $cartDetail->amount = $cartDetail->selling_price * $qty;  
                $cartDetail->subtotal_weight = $cartDetail->weight * $qty;  
                $cartDetail->save();  
                $this->loadCartItems();  
                $this->success('Cart Updated');
                 
            }  
        } catch (\Exception $e) {  
            session()->flash('error', 'Failed to update cart item: ' . $e->getMessage());  
        }  

        return  $cartDetail;
    }  
  
    public function removeCartItem($cartDetailId)  
    {  
        try {  
            $cartDetail = SalesCartDetail::find($cartDetailId);  
            if ($cartDetail) {  
                $cartDetail->delete();  
                $this->loadCartItems();  
                $this->success('Cart Updated');
            }  
        } catch (\Exception $e) {  
            session()->flash('error', 'Failed to remove cart item: ' . $e->getMessage());  
        }  

        return  $cartDetail;
    }  
  
    public function calculateTotal()  
    {  
        $total = 0;  
        foreach ($this->cartItems as $item) {  
            $total += $item->amount;  
        }  
        return $total;  
    }  
  
    public function calculateDiscount()  
    {  
        // Example discount calculation (15% of total)  
        $total = $this->calculateTotal();  
        return $total * 0.15;  
    }  
  
    public function calculateShipping()  
    {  
        // Example shipping cost  
        return 30000;  
    }  
  
    public function calculateVAT()  
    {  
        // Example VAT calculation (10% of total)  
        $total = $this->calculateTotal();  
        return $total * 0.1;  
    }  
  
    public function calculateFinalTotal()  
    {  
        $total = $this->calculateTotal();  
        $discount = $this->calculateDiscount();  
        $shipping = $this->calculateShipping();  
        $vat = $this->calculateVAT();  
        return $total - $discount + $shipping + $vat;  
    }  
  
    public function calculateHemat()  
    {  
        return 0;
    }  

    
  
 
  
}  
