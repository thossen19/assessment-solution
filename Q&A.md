# Question and Answer

**1 .Technical Approach**
Question: Which critical bug did you find most challenging to fix, and why?
Answer: The most challenging bug was the tax calculation returning $10.00 instead of $7.25.
Question: What was your debugging process for identifying the root causes?
Answer: following critical bug did you find most challenging to fix:
* Isolate Before Integrating - Test components individually
* Reproduce Consistently - Create reproducible test cases
* Compare Contexts - Check different execution environments
* Follow Data Flow - Trace from input to output
* Minimal Changes - Fix only what's broken
* Verify Extensively - Prevent regression issues

**2. Feature Implementation**
Question Which incomplete feature(s) did you choose to implement, and why?
Incomplete Features Implemented: 

* PDF Generation System. Why: Critical for professional invoice
* Dynamic Tax Rate Loading. Why: Required for multi-region business operations
* Discount Business Rules. Why: Automated business logic for customer pricing
* Comprehensive Input Validation. Why: Data integrity and security requirement
* Sequential Invoice Numbering. Why: Professional business requirement, What Was Incomplete
Question:  If you implemented PDF generation, which library did you choose and what influenced your decision? Answer : dompdf

**3. Code Quality**

Question: What improvements did you make to the codebase beyond the required fixes?
Answer: 
**Improvements**
* Architecture & Design Improvements - Interface-Based Design.
* PSR-4 Namespace Organization. 
* Security & Robustness - Enhanced Input Validation.
* Memory-Efficient JSON Handling.
* Testing Infrastructure - Comprehensive Test . 
* Development Experience - Composer Integration. 
* Business Value Additions - Professional Features. 

Question: Are there any areas of the code you would refactor further given more time? Please explain.
Code Refactoring
* Invoice Class - Separation of Concerns. Invoice Calculator - Code Duplication. 
* PDFGenerator - Mixed Responsibilities. Error Handling - Inconsistent Patterns. 
* Data Validation - Scattered Logic. Test Coverage Gaps. API Design Inconsistencies. Missing Features for Production. Document preparation

**4. Testing**
Question: Describe your approach to testing your changes.
Answer: 
Testing Approach for Invoice System Changes
* Unit Tests: Test each class/method individually with PHPUnit
* Integration Tests: Test component interactions (file I/O, tax system, PDF generation) 
* Edge Cases: Boundary values, invalid inputs, error scenarios 
* Regression Tests: Specific tests for known bugs (qty/quantity mismatch, file overwrites)
* Performance Tests: Large datasets, concurrent operations 
Test Isolation: Temporary files, cleanup between tests 
* CI/CD: Automated test execution, 90%+ coverage requirement


Question: Did you add any new tests? If so, what do they cover?
Answer: Yes I added following test and coverage:
* tests/InvoiceTest.php
Coverage: Basic invoice functionality
* test_discount_application.php
Coverage: Discount logic scenarios
* test_pdf_generation.php
Coverage: PDF generation workflow
* test_input_validation.php
Coverage: Input validation scenarios

**5. Technical Decisions**
Question: What assumptions did you make while completing this assessment?
Answer: 
Assumptions Made
* Code Quality: Current tests failing indicate bugs, not test issues 
* File Structure: Existing directory layout is intentional and functional 
* Dependencies: Dompdf optional dependency is by design 
* Data Format: JSON file structure is stable and won't change 
* Business Rules: Discount rules (>=$1000, no sale items) are correct 
* Tax System: External tax rates JSON file is reliable source of truth 
* 	Environment: Single-process operation (no concurrency concerns) 
* Scale: Current file-based storage is sufficient for expected load 
* Testing: Manual test files are temporary, PHPUnit would be preferred 

Question: If you encountered any blockers or unclear requirements, how did you resolve them?
Answer: Blockers Encountered are:

* Failing Tests
Issue: InvoiceTest.php shows 3 failing tests
Resolution: Analyzed test code vs implementation, identified qty/quantity field mismatch and file overwrite bugs
* Mixed Code Quality
Issue: Inconsistent error handling, code duplication, mixed responsibilities
Resolution: Documented specific areas needing refactoring rather than making assumptions
* Unclear Business Logic
Issue: Business rules scattered across classes
Resolution: Mapped out current rules from code comments and implementation.

**Resolution Strategy**
For Ambiguous Requirements:

Analyzed existing code behavior as source of truth
Documented assumptions clearly Proposed solutions that maintain backward compatibility 
For Technical Uncertainties:Examined test failures to understand intended vs actual behavior, Used code comments and method names to infer requirements Focused on fixable issues rather than architectural changes

For Missing Information:Assessed current state as baseline,Provided recommendations for missing features,Maintained existing functionality while suggesting improvements,Key Approach: When requirements were unclear, I analyzed the existing implementation and tests to understand the intended behavior, then documented findings and recommendations rather than making potentially incorrect changes.

**6. Time Management**

Question : Approximately how much time did you spend on this assessment? : 
Answer: 4 Hours and 33 minutes

Question: If you had an additional 2 hours, what would you prioritize?
Answer: Improve error handling and Code Quality 


