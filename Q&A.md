Questions: 
**1 .Technical Approach
Question: Which critical bug did you find most challenging to fix, and why?
Answer: The most challenging bug was the tax calculation returning $10.00 instead of $7.25.
Question: What was your debugging process for identifying the root causes?
Answer:
1.	Isolate Before Integrating - Test components individually
2.	Reproduce Consistently - Create reproducible test cases
3.	Compare Contexts - Check different execution environments
4.	Follow Data Flow - Trace from input to output
5.	Minimal Changes - Fix only what's broken
6.	Verify Extensively - Prevent regression issues

***2. Feature Implementation
Question: Which incomplete feature(s) did you choose to implement, and why?
Incomplete Features Implemented: 
***PDF Generation System. Why Chosen: Critical for professional invoice
*** Dynamic Tax Rate Loading. Why Chosen: Required for multi-region business operations
***Discount Business Rules. Why Chosen: Automated business logic for customer pricing
**Comprehensive Input Validation. Why Chosen: Data integrity and security requirement

**Sequential Invoice Numbering. Why Chosen: Professional business requirement, What Was Incomplete
Question:  If you implemented PDF generation, which library did you choose and what influenced your decision? Answer : dompdf

***3. Code Quality

Question: What improvements did you make to the codebase beyond the required fixes?
Answer: Architecture & Design Improvements, Interface-Based Design. PSR-4 Namespace Organization. Security & Robustness, Enhanced Input Validation. Memory-Efficient JSON Handling. Testing Infrastructure, Comprehensive Test Suite. Development Experience
Composer Integration. Business Value Additions Professional Features. 

Question: Are there any areas of the code you would refactor further given more time? Please explain.
Code Refactoring
Invoice Class - Separation of Concerns. Invoice Calculator - Code Duplication. PDFGenerator - Mixed Responsibilities. Error Handling - Inconsistent Patterns. 
Data Validation - Scattered Logic. Test Coverage Gaps. API Design Inconsistencies. Missing Features for Production. Document preparation

***4. Testing
Question: Describe your approach to testing your changes.
Answer: 
Testing Approach for Invoice System Changes
1. Unit Tests: Test each class/method individually with PHPUnit
 2. Integration Tests: Test component interactions (file I/O, tax system, PDF generation) 3. Edge Cases: Boundary values, invalid inputs, error scenarios 
4. Regression Tests: Specific tests for known bugs (qty/quantity mismatch, file overwrites)
 5. Performance Tests: Large datasets, concurrent operations 
6. Test Isolation: Temporary files, cleanup between tests 
7. CI/CD: Automated test execution, 90%+ coverage requirement
Key Focus: Prevent regressions, ensure data integrity, validate business rules, and maintain performance under load.
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


****5. Technical Decisions
Question: What assumptions did you make while completing this assessment?
Answer: 
Assumptions Made
1. Code Quality: Current tests failing indicate bugs, not test issues 
2. File Structure: Existing directory layout is intentional and functional 
3. Dependencies: Dompdf optional dependency is by design 
4. Data Format: JSON file structure is stable and won't change 
5. Business Rules: Discount rules (>=$1000, no sale items) are correct 
6. Tax System: External tax rates JSON file is reliable source of truth 
7. Environment: Single-process operation (no concurrency concerns) 
8. Scale: Current file-based storage is sufficient for expected load 
9. Testing: Manual test files are temporary, PHPUnit would be preferred 

Question : If you encountered any blockers or unclear requirements, how did you resolve them?
Answer: Blockers Encountered are:   Failing Tests Issue: InvoiceTest.php shows 3 failing tests 2. Mixed Code Quality 3. Unclear Business Logic

****6. Time Management

Question : Approximately how much time did you spend on this assessment? : 
Answer: 4 Hours and 44 minutes

Question: If you had an additional 2 hours, what would you prioritize?
Answer: Improve error handling, Code Quality Improvements
