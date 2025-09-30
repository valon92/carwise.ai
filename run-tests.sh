#!/bin/bash

# CarWise.ai Test Runner
# This script runs all tests (backend and frontend) and generates reports

set -e

echo "🚀 CarWise.ai Test Suite"
echo "========================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if required tools are installed
check_dependencies() {
    print_status "Checking dependencies..."
    
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed or not in PATH"
        exit 1
    fi
    
    if ! command -v composer &> /dev/null; then
        print_error "Composer is not installed or not in PATH"
        exit 1
    fi
    
    if ! command -v npm &> /dev/null; then
        print_error "NPM is not installed or not in PATH"
        exit 1
    fi
    
    print_success "All dependencies are available"
}

# Setup test environment
setup_test_env() {
    print_status "Setting up test environment..."
    
    # Copy .env file if it doesn't exist
    if [ ! -f .env ]; then
        if [ -f env.production.example ]; then
            cp env.production.example .env
            print_success "Copied env.production.example to .env"
        else
            print_warning ".env file not found and no example available"
        fi
    fi
    
    # Generate application key if needed
    if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
        php artisan key:generate --force
        print_success "Generated application key"
    fi
    
    # Install PHP dependencies
    print_status "Installing PHP dependencies..."
    composer install --quiet
    
    # Install Node dependencies
    print_status "Installing Node.js dependencies..."
    npm ci --silent
    
    # Prepare database
    print_status "Preparing test database..."
    touch database/database.sqlite
    php artisan migrate:fresh --env=testing --quiet
    
    print_success "Test environment setup complete"
}

# Run backend tests
run_backend_tests() {
    print_status "Running backend tests..."
    echo "================================"
    
    # Run PHPUnit tests
    if php artisan test --env=testing; then
        print_success "Backend tests passed"
        return 0
    else
        print_error "Backend tests failed"
        return 1
    fi
}

# Run frontend tests
run_frontend_tests() {
    print_status "Running frontend tests..."
    echo "================================="
    
    # Run Vitest
    if npm run test -- --run; then
        print_success "Frontend tests passed"
        return 0
    else
        print_error "Frontend tests failed"
        return 1
    fi
}

# Run code quality checks
run_code_quality() {
    print_status "Running code quality checks..."
    echo "======================================="
    
    # PHP syntax check
    print_status "Checking PHP syntax..."
    find app -name "*.php" -exec php -l {} \; > /dev/null
    
    # Check for common issues
    print_status "Running basic code analysis..."
    
    # Check for TODO comments
    todo_count=$(find app resources/js -type f \( -name "*.php" -o -name "*.js" -o -name "*.vue" \) -exec grep -l "TODO\|FIXME\|HACK" {} \; | wc -l)
    if [ $todo_count -gt 0 ]; then
        print_warning "Found $todo_count files with TODO/FIXME/HACK comments"
    fi
    
    # Check for console.log in production files
    console_log_count=$(find resources/js -name "*.js" -o -name "*.vue" | xargs grep -l "console\.log" | wc -l)
    if [ $console_log_count -gt 0 ]; then
        print_warning "Found $console_log_count files with console.log statements"
    fi
    
    print_success "Code quality checks completed"
}

# Run security audit
run_security_audit() {
    print_status "Running security audit..."
    echo "=========================="
    
    # PHP security audit
    print_status "Auditing PHP dependencies..."
    if composer audit --quiet; then
        print_success "PHP dependencies are secure"
    else
        print_warning "PHP dependencies have security issues"
    fi
    
    # Node.js security audit
    print_status "Auditing Node.js dependencies..."
    if npm audit --audit-level=moderate --silent; then
        print_success "Node.js dependencies are secure"
    else
        print_warning "Node.js dependencies have security issues"
    fi
}

# Generate test report
generate_report() {
    print_status "Generating test report..."
    
    echo ""
    echo "📊 Test Report Summary"
    echo "======================"
    echo "Timestamp: $(date)"
    echo "PHP Version: $(php --version | head -n 1)"
    echo "Node Version: $(node --version)"
    echo "NPM Version: $(npm --version)"
    echo ""
    
    if [ $backend_result -eq 0 ]; then
        echo -e "Backend Tests: ${GREEN}PASSED${NC}"
    else
        echo -e "Backend Tests: ${RED}FAILED${NC}"
    fi
    
    if [ $frontend_result -eq 0 ]; then
        echo -e "Frontend Tests: ${GREEN}PASSED${NC}"
    else
        echo -e "Frontend Tests: ${RED}FAILED${NC}"
    fi
    
    echo ""
}

# Main execution
main() {
    echo "Starting test suite at $(date)"
    echo ""
    
    # Initialize result variables
    backend_result=1
    frontend_result=1
    
    # Run all checks
    check_dependencies
    setup_test_env
    
    echo ""
    print_status "Running test suites..."
    echo ""
    
    # Run backend tests
    if run_backend_tests; then
        backend_result=0
    fi
    
    echo ""
    
    # Run frontend tests
    if run_frontend_tests; then
        frontend_result=0
    fi
    
    echo ""
    
    # Run additional checks
    run_code_quality
    echo ""
    run_security_audit
    
    echo ""
    generate_report
    
    # Exit with error if any tests failed
    if [ $backend_result -ne 0 ] || [ $frontend_result -ne 0 ]; then
        print_error "Some tests failed"
        exit 1
    else
        print_success "All tests passed! 🎉"
        exit 0
    fi
}

# Run main function
main "$@"

