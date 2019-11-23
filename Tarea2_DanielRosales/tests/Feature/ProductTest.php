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
    /*---------------CREATE-----------------*/
	/**     * CREATE-1     */
    /** @test */
    public function test_client_can_create_a_product()
    {
        // Given
        $productData = [
            'name' => 'Super Product',
            'price' => '23.30'
        ];
        // When
        $response = $this->json('POST', '/api/products', $productData); 
        // Then
        // Assert it sends the correct HTTP Status
        $response->assertStatus(201);
        // Assert the response has the correct structure
        $response->assertJsonStructure([
            'id',
            'name',
            'price'
        ]);
        // Assert the product was created
        // with the correct data
        $response->assertJsonFragment([
            'name' => 'Super Product',
            'price' => '23.30'
        ]);
        $body = $response->decodeResponseJson();
        // Assert product is on the database
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $body['id'],
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
            'price' => '23.30'
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
            'name' => 'Super product'
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
            'name' => 'Super product',
            'price' => 'hoye esto no es un precio'
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
            'name' => 'Super product',
            'price' => '-1'
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
	/*---------------------CREATE-----------------*/
	
	
	
    /*---------------------SHOW-----------------------*/
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
        $valor = $response->decodeResponseJson();
        $this->assertJsonStringEqualsJsonString(
            json_encode($producto),
            json_encode($valor)
        );
    }
    /**     * SHOW-2     */
    /** @test */
    public function test_client_cant_show_a_product_ID_does_not_exist()
    {
        //Given
        $producto = factory(Product::class)->create();
        $nombre = $producto->name;
        $precio = $producto->price;
        $id = $producto->id;
        $response = $this->json('GET', '/api/products/-20');
        $response->assertStatus(404);
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
    }
	/*---------------------SHOW-----------------------*/
	
	/*---------------------DELETE---------------------*/
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
		
        $response = $this->call('DELETE', '/api/products/-20');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertDatabaseHas('products', [
            'id' => $id,
            'name' => $nombre,
            'price' => $precio
        ]);
    }
	/*---------------------DELETE---------------------*/
	
	/*---------------------update---------------------*/
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
            'name' => 'Producto 2',
            'price' => '23.30'
        ];
        $response = $this->json('PUT', '/api/products/'. $id .'', $productData);
        $this->assertEquals(200, $response->getStatusCode());
        $valor = $this->json('GET', '/api/products/'. $id .'');
        $this->assertJsonStringNotEqualsJsonString(
            json_encode($producto),
            json_encode($valor)
        );
		
        $this->assertDatabaseHas(
            'products',
            [
                'id' => $id,
                'name' => 'Producto 2',
                'price' => '23.30'
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
            'name' => 'Producto 2',
            'price' => ':('
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
            json_encode($valor->decodeResponseJson())
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
            'price' => '-1'
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
            json_encode($valor->decodeResponseJson())
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
            'price' => '-1'
        ];
        $response = $this->json('PUT', '/api/products/-20', $productData);
        $this->assertEquals(422, $response->getStatusCode());
        $response->assertJsonStructure([
            "errors" => [
                "code",
                "title",
                "type"]
        ]);
    }
	/*---------------------update---------------------*/
	
	/*---------------------LIST---------------------*/
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