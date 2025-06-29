# Changelog

All notable changes to the phpFormStep library will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-06-29

### Added
- Initial release of phpFormStep library
- Professional multi-step form management with advanced configuration
- Session-based state management with persistence across requests
- Laravel-style validation engine with comprehensive rule support
- Flexible step file and handler mapping system
- Support for create/edit modes with primary key tracking
- Navigation control with required save steps
- Autoloader and Composer support
- Library protection against direct access
- Comprehensive documentation and examples

### Features
- **FormStepConfig**: Advanced configuration management
- **FormStepState**: Session-based state persistence  
- **FormStepValidator**: Laravel-style validation rules
- **FormStepManager**: Main orchestrator and controller
- **Bootstrap system**: Professional library loading
- **Protection layer**: Prevents unauthorized access and modifications

### Validation Rules
- `required` - Field must have a value
- `email` - Must be valid email address
- `numeric` - Must be numeric value
- `url` - Must be valid URL
- `min:n` - Minimum character length
- `max:n` - Maximum character length
- `min_value:n` - Minimum numeric value
- `max_value:n` - Maximum numeric value

### Compatibility
- PHP 8.0+ required
- Session support required
- Framework agnostic design
- PSR-4 autoloading compliant
