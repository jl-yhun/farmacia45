<?php

namespace Tests\Unit;

use App\Helpers\LoggerBuilder;
use App\Producto;
use App\Repositories\IProductosRepository;
use App\Repositories\TagRepository;
use App\Tag;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class TagRepositoryTest extends TestCase
{
    private $_loggerMock;
    private $_productoRepoMock;
    private $_tagModelMock;
    private $_productoModelMock;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var \Mockery\MockInterface $_loggerMock */
        $this->_loggerMock = Mockery::mock(LoggerBuilder::class);
        $this->_loggerMock
            ->shouldReceive('success', 'user_id', 'module', 'method', 'link_id', 'after', 'log', 'error', 'exception')
            ->andReturn($this->_loggerMock);

        $this->_tagModelMock = Mockery::mock(Tag::class);

        $this->_tagModelMock
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $this->_productoModelMock = Mockery::mock(Producto::class);

        $this->_productoModelMock
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $this->_productoRepoMock = Mockery::mock(IProductosRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_link_happy_path()
    {
        // Arrange
        $input = [
            'nombre' => 'no-disponible' // It does not exist
        ];

        // Assert
        DB::shouldReceive('beginTransaction')
            ->times(2);

        DB::shouldReceive('commit')
            ->times(2);

        DB::shouldReceive('rollback')
            ->never();

        DB::shouldReceive('table')
            ->with('tags')
            ->times(3)
            ->andReturnSelf();

        DB::shouldReceive('table')
            ->with('tags_models')
            ->once()
            ->andReturnSelf();

        DB::shouldReceive('where')
            ->with('nombre', $input['nombre'])
            ->times(2)
            ->andReturnSelf();

        DB::shouldReceive('first')
            ->andReturns(null, $this->_tagModelMock);

        DB::shouldReceive('insert')
            ->times(2)
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));

        $this->_loggerMock
            ->shouldReceive('description')
            ->with('App\Repositories\TagRepository::link finished')
            ->andReturn($this->_loggerMock)
            ->once();

        $this->_loggerMock
            ->shouldReceive('description')
            ->with('App\Repositories\TagRepository::create finished')
            ->andReturn($this->_loggerMock)
            ->once();

        // Act
        $repo = new TagRepository($this->_loggerMock, $this->_productoRepoMock);
        $actual = $repo->link(Producto::class, 1, $input);

        // Assert
        $this->assertEquals(true, $actual);
    }

    public function test_link_when_exist()
    {
        // Arrange
        $input = [
            'nombre' => 'no-disponible' // It DOES exist
        ];

        // Assert
        DB::shouldReceive('beginTransaction')
            ->times(1);

        DB::shouldReceive('commit')
            ->times(1);

        DB::shouldReceive('rollback')
            ->never();

        DB::shouldReceive('table')
            ->with('tags')
            ->times(1)
            ->andReturnSelf();

        DB::shouldReceive('table')
            ->with('tags_models')
            ->once()
            ->andReturnSelf();

        DB::shouldReceive('where')
            ->with('nombre', $input['nombre'])
            ->times(1)
            ->andReturnSelf();

        DB::shouldReceive('first')
            ->andReturns($this->_tagModelMock);

        DB::shouldReceive('insert')
            ->times(1)
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));

        $this->_loggerMock
            ->shouldReceive('description')
            ->with('App\Repositories\TagRepository::link finished')
            ->andReturn($this->_loggerMock)
            ->once();

        $this->_loggerMock
            ->shouldReceive('description')
            ->with('App\Repositories\TagRepository::create finished')
            ->andReturn($this->_loggerMock)
            ->never();

        // Act
        $repo = new TagRepository($this->_loggerMock, $this->_productoRepoMock);
        $actual = $repo->link(Producto::class, 1, $input);

        // Assert
        $this->assertEquals(true, $actual);
    }

    public function test_link_exception()
    {
        // Arrange
        $input = [
            'nombre' => 'no-disponible'
        ];

        // Assert
        DB::shouldReceive('beginTransaction')
            ->times(1);

        DB::shouldReceive('commit')
            ->times(0);

        DB::shouldReceive('rollback')
            ->times(1);

        DB::shouldReceive('table')
            ->with('tags')
            ->times(1)
            ->andReturnSelf();

        DB::shouldReceive('table')
            ->with('tags_models')
            ->once()
            ->andReturnSelf();

        DB::shouldReceive('where')
            ->with('nombre', $input['nombre'])
            ->times(1)
            ->andReturnSelf();

        DB::shouldReceive('first')
            ->andReturns($this->_tagModelMock);

        DB::shouldReceive('insert')
            ->times(1)
            ->andThrow(new Exception());

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));

        $this->_loggerMock
            ->shouldReceive('description')
            ->with('App\Repositories\TagRepository::link finished with error')
            ->andReturn($this->_loggerMock)
            ->once();

        $this->_loggerMock
            ->shouldReceive('description')
            ->with('App\Repositories\TagRepository::create finished')
            ->andReturn($this->_loggerMock)
            ->times(0);

        // Act
        $repo = new TagRepository($this->_loggerMock, $this->_productoRepoMock);
        $actual = $repo->link(Producto::class, 1, $input);

        // Assert
        $this->assertEquals(false, $actual);
    }

    public function test_unlink_happy_path()
    {
        // Assert
        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('table->where->delete')
            ->once();

        DB::shouldReceive('commit')
            ->once();

        DB::shouldReceive('rollback')
            ->never();

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(new User(['id' => 1]));

        $this->_loggerMock
            ->shouldReceive('description')
            ->with(TagRepository::class . '::unlink finished for tageable ' . $this->_productoModelMock->id)
            ->andReturn($this->_loggerMock)
            ->once();


        // Act
        $repo = new TagRepository($this->_loggerMock, $this->_productoRepoMock);
        $actual = $repo->unlink(Producto::class, $this->_productoModelMock, $this->_tagModelMock);

        // Assert
        $this->assertEquals(true, $actual);
    }

    public function test_unlink_exception()
    {
        // Assert
        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('table->where->delete')
            ->once()
            ->andThrow(new Exception());

        DB::shouldReceive('commit')
            ->never();

        DB::shouldReceive('rollback')
            ->once();

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(new User(['id' => 1]));

        $this->_loggerMock
            ->shouldReceive('description')
            ->with(TagRepository::class . '::unlink finished with error for tageable ' . $this->_productoModelMock->id)
            ->andReturn($this->_loggerMock)
            ->once();


        // Act
        $repo = new TagRepository($this->_loggerMock, $this->_productoRepoMock);
        $actual = $repo->unlink(Producto::class, $this->_productoModelMock, $this->_tagModelMock);

        // Assert
        $this->assertEquals(false, $actual);
    }
}
