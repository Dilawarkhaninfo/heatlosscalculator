<?php

/** @var \Illuminate\Database\Eloquent\Collection $products */
$categoryList = \App\Models\Category::getActiveAsTree();

?>

<x-app-layout>
    <x-category-list :category-list="$categoryList" class="-ml-5 -mt-5 -mr-5 px-4" />

    <div class="flex gap-2 items-center p-3 pb-0" x-data="{
            selectedSort: '{{ request()->get('sort', '-updated_at') }}',
            searchKeyword: '{{ request()->get('search') }}',
            updateUrl() {
                const params = new URLSearchParams(window.location.search)
                if (this.selectedSort && this.selectedSort !== '-updated_at') {
                    params.set('sort', this.selectedSort)
                } else {
                    params.delete('sort')
                }

                if (this.searchKeyword) {
                    params.set('search', this.searchKeyword)
                } else {
                    params.delete('search')
                }
                window.location.href = window.location.origin + window.location.pathname + '?'
                + params.toString();
            }
        }">
        <form action="" method="GET" class="flex-1" @submit.prevent="updateUrl">
            <x-input type="text" name="search" placeholder="Search for the products" x-model="searchKeyword" />
        </form>
        <x-input x-model="selectedSort" @change="updateUrl" type="select" name="sort" class="w-full focus:border-purple-600 focus:ring-purple-600 border-gray-300 rounded">
            <option value="price">Price (ASC)</option>
            <option value="-price">Price (DESC)</option>
            <option value="title">Title (ASC)</option>
            <option value="-title">Title (DESC)</option>
            <option value="-updated_at">Last Modified at the top</option>
            <option value="updated_at">Last Modified at the bottom</option>
        </x-input>

    </div>

    <?php if ($products->count() === 0) : ?>
        <div>
            <div class="card mt-[20px]">
                <div class="card-body border">
                    <div class="calculator">
                        <div class="display-screen">
                            <input v-model="expression" class="form-control w-[100%] py-[14px]" readonly />
                        </div>

                        <div class="buttons">
                            <div class="row">
                                <button @click="appendToExpression('7')" class="btn btn-primary">7</button>
                                <button @click="appendToExpression('8')" class="btn btn-primary">8</button>
                                <button @click="appendToExpression('9')" class="btn btn-primary">9</button>
                                <button @click="appendToExpression('/')" class="btn btn-primary">/</button>
                            </div>
                            <div class="row">
                                <button @click="appendToExpression('4')" class="btn btn-primary">4</button>
                                <button @click="appendToExpression('5')" class="btn btn-primary">5</button>
                                <button @click="appendToExpression('6')" class="btn btn-primary">6</button>
                                <button @click="appendToExpression('*')" class="btn btn-primary">*</button>
                            </div>
                            <div class="row">
                                <button @click="appendToExpression('1')" class="btn btn-primary">1</button>
                                <button @click="appendToExpression('2')" class="btn btn-primary">2</button>
                                <button @click="appendToExpression('3')" class="btn btn-primary">3</button>
                                <button @click="appendToExpression('-')" class="btn btn-primary">-</button>
                            </div>
                            <div class="row">
                                <button @click="appendToExpression('0')" class="btn btn-primary">0</button>
                                <button @click="appendToExpression('.') " class="btn btn-primary">.</button>
                                <button @click="calculateResult()" class="btn btn-primary">=</button>
                                <button @click="appendToExpression('+')" class="btn btn-primary">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="grid gap-4 grig-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-3">
            @foreach($products as $product)
            <!-- Product Item -->
            <div x-data="productItem({{ json_encode([
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $product->image ?: '/img/noimage.png',
                        'title' => $product->title,
                        'price' => $product->price,
                        'addToCartUrl' => route('cart.add', $product)
                    ]) }})" class="border border-1 border-gray-200 rounded-md hover:border-purple-600 transition-colors bg-white">
                <a href="{{ route('product.view', $product->slug) }}" class="aspect-w-3 aspect-h-2 block overflow-hidden">
                    <img :src="product.image" alt="" class="object-cover rounded-lg hover:scale-105 hover:rotate-1 transition-transform" />
                </a>
                <div class="p-4">
                    <h3 class="text-lg">
                        <a href="{{ route('product.view', $product->slug) }}">
                            {{$product->title}}
                        </a>
                    </h3>
                    <h5 class="font-bold">${{$product->price}}</h5>
                </div>
                <div class="flex justify-between py-3 px-4">
                    <button class="btn-primary" @click="addToCart()">
                        Add to Cart
                    </button>
                </div>
            </div>
            <!--/ Product Item -->
            @endforeach
        </div>
        {{$products->appends(['sort' => request('sort'), 'search' => request('search')])->links()}}
    <?php endif; ?>

    <script>
        export default {
            data() {
                return {
                    expression: '',
                };
            },
            methods: {
                appendToExpression(value) {
                    this.expression += value;
                },
                calculateResult() {
                    try {
                        this.expression = eval(this.expression).toString();
                    } catch (error) {
                        this.expression = 'Error';
                    }
                },
            },
        };
    </script>


    <style scoped>
        .calculator {
            display: flex;
            flex-direction: column;
            max-width: 800px;
            margin: 0 auto;
        }

        .display-screen {
            margin-bottom: 10px;
        }

        .buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            box-shadow: 5px;
        }

        button {
            width: 100%;
            padding: 20px;
            margin: 2px;
            font-size: 18px;
            text-align: center;

        }
    </style>
</x-app-layout>
