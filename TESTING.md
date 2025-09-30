# Testing Guide - CarWise.ai

This guide covers all aspects of testing in the CarWise.ai application, including setup, running tests, and best practices.

## 🏗️ Testing Architecture

Our testing strategy follows a comprehensive approach covering:

- **Unit Tests** - Test individual components and functions in isolation
- **Integration Tests** - Test interactions between different parts of the system  
- **Feature Tests** - Test complete user workflows and API endpoints
- **End-to-End Tests** - Test the full application from user perspective

## 📋 Test Structure

```
tests/
├── Unit/                    # PHPUnit unit tests
│   ├── AIDiagnosisServiceTest.php
│   ├── LanguageDetectionTest.php
│   └── ExampleTest.php
├── Feature/                 # PHPUnit feature tests  
│   ├── CarApiTest.php
│   ├── DiagnosisApiTest.php
│   └── ExampleTest.php
└── frontend/               # Frontend tests (Vitest)
    ├── components/
    │   └── Navbar.test.js
    ├── services/
    │   └── api.test.js
    ├── composables/
    │   └── useAuth.test.js
    └── setup.js
```

## 🚀 Quick Start

### Run All Tests
```bash
# Using the provided script (recommended)
./run-tests.sh

# Or manually:
# Backend tests
php artisan test

# Frontend tests  
npm run test
```

### Run Specific Test Suites

```bash
# Backend only
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Frontend only
npm run test

# With coverage
npm run test:coverage

# Interactive mode
npm run test:ui
```

## 🔧 Backend Testing (PHPUnit)

### Configuration
- **Framework**: PHPUnit 11.x
- **Database**: SQLite in-memory for testing
- **Environment**: Uses `testing` environment with `.env.testing`

### Test Categories

#### Unit Tests
Test individual classes and methods in isolation:

```php
<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AIDiagnosisService;

class AIDiagnosisServiceTest extends TestCase
{
    public function test_analyzes_diagnosis_with_valid_data()
    {
        $service = new AIDiagnosisService();
        $data = [
            'car_brand' => 'Toyota',
            'symptoms' => ['Strange noises'],
            'problem_description' => 'Engine issues'
        ];
        
        $result = $service->analyzeDiagnosis($data);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('problem_title', $result);
    }
}
```

#### Feature Tests
Test complete API endpoints and workflows:

```php
<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class CarApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_car()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/cars', [
            'brand_id' => 1,
            'model_id' => 1,
            'year' => 2020,
            'license_plate' => 'TEST-123'
        ]);

        $response->assertStatus(201);
    }
}
```

### Key Testing Features

#### Language Detection Testing
```php
public function test_detects_albanian_language()
{
    $text = 'Motori bën zhurma të çuditshme';
    $result = $this->detectLanguage($text);
    $this->assertEquals('sq', $result);
}
```

#### AI Service Testing
```php
public function test_handles_multilingual_input()
{
    $data = [
        'problem_description' => 'Motori bën zhurma',
        'user_language' => 'sq'
    ];
    
    $result = $this->aiService->analyzeDiagnosis($data);
    $this->assertNotEmpty($result['problem_title']);
}
```

### Database Testing
- Uses `RefreshDatabase` trait for clean state
- Factory classes for generating test data
- In-memory SQLite for speed

## 🎨 Frontend Testing (Vitest)

### Configuration
- **Framework**: Vitest with Vue Test Utils
- **Environment**: jsdom for DOM simulation
- **Mocking**: Vi functions for dependencies

### Test Categories

#### Component Tests
```javascript
import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import Navbar from '@/components/Navbar.vue'

describe('Navbar', () => {
  it('shows login link when not authenticated', () => {
    const wrapper = mount(Navbar)
    expect(wrapper.find('[data-testid="login-link"]').exists()).toBe(true)
  })
})
```

#### Service Tests
```javascript
import { describe, it, expect, vi } from 'vitest'
import { authAPI } from '@/services/api.js'

describe('Auth API', () => {
  it('calls correct login endpoint', () => {
    const mockPost = vi.fn()
    authAPI.login({ email: 'test@example.com', password: 'password' })
    expect(mockPost).toHaveBeenCalledWith('/login', expect.any(Object))
  })
})
```

#### Composables Tests
```javascript
import { describe, it, expect } from 'vitest'
import { useAuth } from '@/composables/useAuth.js'

describe('useAuth', () => {
  it('initializes with null user', () => {
    const auth = useAuth()
    expect(auth.user.value).toBeNull()
    expect(auth.isAuthenticated.value).toBe(false)
  })
})
```

### Mocking Strategies

#### API Mocking
```javascript
vi.mock('@/services/api.js', () => ({
  authAPI: {
    login: vi.fn(),
    logout: vi.fn(),
    register: vi.fn()
  }
}))
```

#### Router Mocking
```javascript
const mockRouter = {
  push: vi.fn(),
  replace: vi.fn()
}
```

## 🔄 Continuous Integration

### GitHub Actions Workflow

Our CI pipeline runs automatically on:
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop`

#### Pipeline Stages:

1. **Backend Tests**
   - PHP 8.2 setup
   - MySQL service for integration tests
   - Composer dependencies
   - PHPUnit test execution

2. **Frontend Tests**
   - Node.js 18 setup
   - NPM dependencies
   - Vitest execution

3. **Code Quality**
   - PHP CS Fixer (coding standards)
   - ESLint (JavaScript/Vue linting)

4. **Security Audit**
   - Composer audit for PHP vulnerabilities
   - NPM audit for Node.js vulnerabilities

5. **Build Test**
   - Frontend asset compilation
   - Laravel application bootstrap test

### Test Script Features

The `run-tests.sh` script provides:
- ✅ Dependency checking
- ✅ Environment setup
- ✅ Comprehensive test execution
- ✅ Code quality checks
- ✅ Security auditing
- ✅ Detailed reporting

## 📊 Coverage & Reporting

### Backend Coverage
```bash
php artisan test --coverage
```

### Frontend Coverage
```bash
npm run test:coverage
```

Coverage reports are generated in:
- `coverage/backend/` - PHP coverage
- `coverage/frontend/` - JavaScript/Vue coverage

## 🧪 Test Data & Factories

### PHP Factories
```php
// CarFactory.php
class CarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'brand_id' => CarBrand::factory(),
            'year' => $this->faker->numberBetween(1990, date('Y')),
            'license_plate' => strtoupper($this->faker->bothify('???-###'))
        ];
    }
}
```

### Frontend Test Data
```javascript
const mockUser = {
  id: 1,
  name: 'John Doe',
  email: 'john@example.com'
}

const mockCarData = {
  brand_id: 1,
  model_id: 1,
  year: 2020,
  license_plate: 'TEST-123'
}
```

## 🎯 Best Practices

### General Testing Principles
1. **Arrange, Act, Assert** - Structure tests clearly
2. **Descriptive Names** - Test names should describe what they test
3. **Independent Tests** - Each test should run in isolation
4. **Fast Execution** - Keep tests fast and focused
5. **Meaningful Assertions** - Test the right things

### Backend Testing Best Practices
1. Use `RefreshDatabase` for database tests
2. Factory classes for test data generation
3. Mock external services (AI APIs, payment gateways)
4. Test both happy path and error scenarios
5. Use data providers for testing multiple inputs

### Frontend Testing Best Practices
1. Test user interactions, not implementation details
2. Mock external dependencies (APIs, router)
3. Use data-testid attributes for reliable selectors
4. Test accessibility attributes
5. Focus on component behavior, not internal state

### Language Detection Testing
```php
public function test_multilingual_detection_accuracy()
{
    $testCases = [
        ['Motori bën zhurma', 'sq'],
        ['El motor hace ruido', 'es'],
        ['O motor faz barulho', 'pt'],
        ['Le moteur fait du bruit', 'fr']
    ];
    
    foreach ($testCases as [$text, $expected]) {
        $this->assertEquals($expected, $this->detectLanguage($text));
    }
}
```

## 🐛 Debugging Tests

### Backend Debugging
```bash
# Run specific test with verbose output
php artisan test --filter=test_specific_method --verbose

# Debug with dd() in tests
public function test_something()
{
    $result = $this->someMethod();
    dd($result); // This will dump and die
}
```

### Frontend Debugging
```bash
# Run tests in watch mode
npm run test -- --watch

# Debug specific test
npm run test -- --run Navbar.test.js
```

### Common Issues & Solutions

#### Backend Issues
- **Database not found**: Ensure SQLite file exists in database/
- **Missing dependencies**: Run `composer install`
- **Migration issues**: Use `RefreshDatabase` trait

#### Frontend Issues
- **Module not found**: Check path aliases in vitest.config.js
- **Mock not working**: Verify mock placement before imports
- **Component not rendering**: Check for missing dependencies or props

## 📈 Performance Testing

### Load Testing
```bash
# Install artillery for API load testing
npm install -g artillery

# Run load tests
artillery run tests/load/api-load-test.yml
```

### Database Performance
```php
public function test_query_performance()
{
    $this->assertDatabaseQueryCount(5, function () {
        // Code that should execute exactly 5 queries
    });
}
```

## 🔒 Security Testing

### Authentication Tests
```php
public function test_unauthenticated_access_denied()
{
    $response = $this->getJson('/api/cars');
    $response->assertStatus(401);
}
```

### Authorization Tests
```php
public function test_user_cannot_access_other_users_data()
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $user2->id]);
    
    Sanctum::actingAs($user1);
    $response = $this->getJson("/api/cars/{$car->id}");
    $response->assertStatus(404);
}
```

## 📚 Additional Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Vitest Documentation](https://vitest.dev/)
- [Vue Test Utils](https://vue-test-utils.vuejs.org/)
- [Laravel Testing](https://laravel.com/docs/testing)

## 🤝 Contributing to Tests

When adding new features:
1. Write tests first (TDD approach)
2. Ensure good test coverage (aim for >80%)
3. Update this documentation if needed
4. Run full test suite before committing

## 📞 Support

If you encounter issues with tests:
1. Check this documentation first
2. Review error messages carefully
3. Ensure all dependencies are installed
4. Check GitHub Actions for CI failures

---

**Happy Testing! 🧪✨**

