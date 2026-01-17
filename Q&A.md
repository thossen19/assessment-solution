Question and Answer:

1 .Technical Approach
Question: Which critical bug did you find most challenging to fix, and why?
Answer: The most challenging bug was the tax calculation returning $10.00 instead of $7.25.
Question: What was your debugging process for identifying the root causes?
Answer:
a)	Isolate Before Integrating - Test components individually
b)	Reproduce Consistently - Create reproducible test cases
c)	Compare Contexts - Check different execution environments
d)	Follow Data Flow - Trace from input to output
e)	Minimal Changes - Fix only what's broken
f)	Verify Extensively - Prevent regression issues

2. Feature Implementation
Question Which incomplete feature(s) did you choose to implement, and why?
Incomplete Features Implemented: 
o	PDF Generation System. Why: Critical for professional invoice
o	Dynamic Tax Rate Loading. Why: Required for multi-region business operations
o	Discount Business Rules. Why: Automated business logic for customer pricing
o	Comprehensive Input Validation. Why: Data integrity and security requirement
o	Sequential Invoice Numbering. Why: Professional business requirement, What Was Incomplete
Question:  If you implemented PDF generation, which library did you choose and what influenced your decision? Answer : dompdf
3. Code Quality

Question: What improvements did you make to the codebase beyond the required fixes?
Answer: Architecture & Design Improvements, Interface-Based Design. PSR-4 Namespace Organization. Security & Robustness, Enhanced Input Validation. Memory-Efficient JSON Handling. Testing Infrastructure, Comprehensive Test Suite. Development Experience
Composer Integration. Business Value Additions Professional Features. 

Question: Are there any areas of the code you would refactor further given more time? Please explain.
Code Refactoring
Invoice Class - Separation of Concerns. Invoice Calculator - Code Duplication. PDFGenerator - Mixed Responsibilities. Error Handling - Inconsistent Patterns. 
Data Validation - Scattered Logic. Test Coverage Gaps. API Design Inconsistencies. Missing Features for Production. Document preparation

4. Testing
Question: Describe your approach to testing your changes.
Answer: 
Testing Approach for Invoice System Changes
•	Unit Tests: Test each class/method individually with PHPUnit
•	Integration Tests: Test component interactions (file I/O, tax system, PDF generation) 3. Edge Cases: Boundary values, invalid inputs, error scenarios 
•	Regression Tests: Specific tests for known bugs (qty/quantity mismatch, file overwrites)
•	Performance Tests: Large datasets, concurrent operations 
•	Test Isolation: Temporary files, cleanup between tests 
•	CI/CD: Automated test execution, 90%+ coverage requirement
•	Key Focus: Prevent regressions, ensure data integrity, validate business rules, and maintain performance under load.
Question: Did you add any new tests? If so, what do they cover?
Answer: Yes I added following test and coverage:
•	tests/InvoiceTest.php
•	Coverage: Basic invoice functionality
•	test_discount_application.php
•	Coverage: Discount logic scenarios
•	test_pdf_generation.php
•	Coverage: PDF generation workflow
•	test_input_validation.php
•	Coverage: Input validation scenarios

5. Technical Decisions
Question: What assumptions did you make while completing this assessment?
Answer: 
Assumptions Made
o	Code Quality: Current tests failing indicate bugs, not test issues 
o	File Structure: Existing directory layout is intentional and functional 
o	Dependencies: Dompdf optional dependency is by design 
o	Data Format: JSON file structure is stable and won't change 
o	Business Rules: Discount rules (>=$1000, no sale items) are correct 
o	Tax System: External tax rates JSON file is reliable source of truth 
o	Environment: Single-process operation (no concurrency concerns) 
o	Scale: Current file-based storage is sufficient for expected load 
o	Testing: Manual test files are temporary, PHPUnit would be preferred 

Question: If you encountered any blockers or unclear requirements, how did you resolve them?
Answer: Blockers Encountered are:   Failing Tests Issue: InvoiceTest.php shows 3 failing tests 2. Mixed Code Quality 3. Unclear Business Logic

6. Time Management

Question : Approximately how much time did you spend on this assessment? : 
Answer: 4 Hours and 33 minutes

Question: If you had an additional 2 hours, what would you prioritize?
Answer: Improve error handling and Code Quality 
