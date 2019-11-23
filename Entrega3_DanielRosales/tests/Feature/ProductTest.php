<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
	/*------------------------CREATE----------------------*/
    /**     * CREATE-1     */
    /** @test */
    public function test_client_can_create_a_product()
    {
        // Given
        $productData = [
            'data' => [
                'type' => "products",
                'attributes' => [
                    'name' => 'Super Product',
                    'price' => '23.30'
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'data' => [
                '*' =>  array_keys((new Product())->toArray())
            ]
        ]);
        // Assert the product was created
        // with the correct data
        $response->assertJsonFragment([
            'name' => 'Super Product',
            'price' => '23.30'
        ]);
        $id =  json_encode($response->baseResponse->original->id);
        // Assert product is on the database
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $id,
                'name' => 'Super Product',
                'price' => '23.30'
            ]
        );
    }
    /**     * CREATE-2     */
    /** @test */
    public function test_client_cant_create_a_product_name_not_sent()
    {
        // Given
        $productData = [
            'data' => [
                'type' => "products",
                'attributes' => [
                    'price' => '23.30'
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
    }
    /**     * CREATE-3     */
    /** @test */
    public function test_client_cant_create_a_product_price_not_sent()
    {
        // Given
        $productData = [
            'data' => [
                'type' => "products",
                'attributes' => [
                    'name' => 'Super product'
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
    }
    /**     * CREATE-4     */
    /** @test */
    public function test_client_cant_create_a_product_price_not_a_number()
    {
        // Given
        $productData = [
            'data' => [
                'type' => "products",
                'attributes' => [
                    'name' => 'Super product',
                    'price' => 'la wea fome'
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
    }
    /**     * CREATE-5     */
    /** @test */
    public function test_client_cant_create_a_product_price_less_than_or_equal_to_zero()
    {
        // Given
        $productData = [
            'data' => [
                'type' => "products",
                'attributes' => [
                    'name' => 'Super product',
                    'price' => '-1'
                ]
            ]
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(422);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
    }
	/*------------------------CREATE----------------------*/
	
	/*------------------------SHOW----------------------*/
    /**     * SHOW-1     */
    /** @test */
    public function test_client_can_show_a_product()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $response = $this->json('GET', '/api/products/'. $id .'');
        $response->assertStatus(200);
        $this->assertJsonStringEqualsJsonString(
            json_encode($producto),
            json_encode($response->baseResponse->original)
        );
    }
    /**     * SHOW-2     */
    /** @test */
    public function  test_client_cant_show_a_product_ID_does_not_exist()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $response = $this->json('GET', '/api/products/-111');
        $response->assertStatus(404);
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
    }
	/*------------------------SHOW----------------------*/
	
	/*------------------------DELETE----------------------*/
    /**     * DELETE-1     */
    /** @test */
    public function test_client_can_delete_products()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $response = $this->call('DELETE', '/api/products/'.$id);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('products', [
            'id' => $id,
            'name' => $nombre,
            'price' => $precio
        ]);
    }
    /**     * DELETE-2     */
    /** @test */
    public function test_client_cant_delete_products_ID_does_not_exist()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $response = $this->call('DELETE', '/api/products/-111');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertDatabaseHas('products', [
            'id' => $id,
            'name' => $nombre,
            'price' => $precio
        ]);
    }
	/*------------------------DELETE----------------------*/
	
	/*------------------------UPDATE----------------------*/
    /**     * UPDATE-1     */
    /** @test */
    public function test_client_can_update_a_product()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $productData = [
            'data' => [
                'type' => "products",
                'attributes' => [
                    'name' => 'Super product',
                    'price' => '15.00'
                ]
            ]
        ];
        $response = $this->json('PUT', '/api/products/'. $id .'', $productData);
        $this->assertEquals(200, $response->getStatusCode());
        $valor = $this->json('GET', '/api/products/'. $id .'');
        $this->assertJsonStringNotEqualsJsonString(
            json_encode($producto),
            json_encode($valor->baseResponse->original)
        );
        //print_r(json_encode($primerValor). ' =/= '. json_encode($segundoValor));
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $id,
                'name' => 'Super product',
                'price' => '15.00'
            ]
        );
    }
    /**     * UPDATE-2     */
    /** @test */
    public function test_client_cant_update_a_product_price_not_number()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $productData = [
            'data' => [
                'type' => "products",
                'attributes' => [
                    'name' => 'Super product',
                    'price' => 'hola bb'
                ]
            ]
        ];
        $response = $this->json('PUT', '/api/products/'. $id .'', $productData);
        $this->assertEquals(422, $response->getStatusCode());
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
        $valor = $this->json('GET', '/api/products/'. $id .'');
        $this->assertJsonStringEqualsJsonString(
            json_encode($producto),
            json_encode($valor->baseResponse->original)
        );
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $id,
                'name' => $nombre,
                'price' => $precio
            ]
        );
    }
    /**     * UPDATE-3     */
    /** @test */
    public function test_client_cant_update_a_product_price_less_than_or_equal_to_zero()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $productData = [
            'name' => 'Producto 2',
            'price' => '-20'
        ];
        $response = $this->json('PUT', '/api/products/'. $id .'', $productData);
        $this->assertEquals(422, $response->getStatusCode());
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
        $valor = $this->json('GET', '/api/products/'. $id .'');
        $this->assertJsonStringEqualsJsonString(
            json_encode($producto),
            json_encode($valor->baseResponse->original)
        );
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $id,
                'name' => $nombre,
                'price' => $precio
            ]
        );
    }
    /**     * UPDATE-4     */
    /** @test */
    public function test_client_cant_update_a_product_ID_not_exist()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $productData = [
            'name' => 'Producto 2',
            'price' => '-20'
        ];
        $response = $this->json('PUT', '/api/products/-1111', $productData);
        $this->assertEquals(422, $response->getStatusCode());
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
    }
	/*------------------------UPDATE----------------------*/
	
	/*------------------------LIST----------------------*/
    /**     * LIST-1     */
    /** @test */
    public function test_client_can_show_all_products()
    {
        //Given
        $producto1 = factory(Product::class)->create();
        $nombre = $producto1->name;
        $precio = $producto1->price;
        $id = $producto1->id;
        //and
        //Given
        $producto2 = factory(Product::class)->create();
        $nombre = $producto2->name;
        $precio = $producto2->price;
        $id = $producto2->id;
        $response = $this->json('GET', '/api/products/');
        $this->assertEquals(200, $response->getStatusCode());
    }
	
    /**     * LIST-2     */
    /** @test */
    public function test_client_can_show_all_products_when_is_empty(){
        $productData = '{"baseResponse":{"headers":{},"original":[],"exception":null}}';
        $response = $this->json('GET', '/api/products/');
        $valor = json_encode($response);
        $this->assertEquals($productData, $valor);
        $this->assertEquals(200, $response->getStatusCode());
    }
}