# Missing API Endpoints Analysis

## Already Implemented Resources
- AccountResource
- ActionResource
- ArticleResource
- CustomerResource
- FileResource
- ObjectResource
- PersonResource
- PurposeResource
- ReceiptResource
- RelationResource
- ReminderResource
- SaleResource
- TaskResource
- TermResource
- UserResource

## Missing Endpoints by Resource

### 1. **AccountResource** (Partially implemented)
Missing endpoints:
- `executeAccountSendLogin` - Send login credentials to account holder
- `searchAccount` - Search accounts (partially implemented, only by email)

### 2. **ActionResource** (Mostly complete)
Missing endpoints:
- `delAction` - Delete action

### 3. **ArticleResource** (Basic implementation)
Missing endpoints:
- `delArticle` - Delete article
- `listArticlecat` - List article categories

### 4. **CustomerResource** (Well implemented)
Missing endpoints:
- `addCustomerCat` - Add customer category
- `delCustomerCat` - Delete customer category
- `addCustomerProject` - Add customer project
- `updateCustomerProject` - Update customer project
- `delCustomerProject` - Delete customer project
- `addCustomerReceipt` - Add customer receipt
- `updateCustomerReceipt` - Update customer receipt
- `getCustomerHistory` - Get customer history
- `executeCustomerProceeding` - Execute customer proceeding

### 5. **ObjectResource** (Basic implementation)
Missing endpoints:
- `addCustomerobject` - Add customer object
- `updateCustomerobject` - Update customer object
- `delCustomerobject` - Delete customer object
- `getCustomerobject` - Get customer object
- `searchCustomerobject` - Search customer objects
- `addObjectAndCustomerobject` - Add object and customer object together
- `updateObjectAndCustomerobject` - Update object and customer object together

### 6. **PersonResource** (Complete for basic operations)
No missing CRUD endpoints

### 7. **ReceiptResource** (Partially implemented)
Missing endpoints:
- `addReceiptFile` - Add receipt file
- `delReceiptFile` - Delete receipt file
- `addReceiptPos` - Add receipt position (partially implemented)
- `delReceiptPos` - Delete receipt position
- `addReceiptTextPos` - Add receipt text position (partially implemented)
- `executeReceiptSendMail` - Send receipt via email
- `executeReceiptSendOB24` - Send receipt via OB24
- `finishReceiptOrderStatus` - Finish receipt order status
- `updateReceiptStatus` - Update receipt status

### 8. **RelationResource** (Complete for basic operations)
Missing endpoints:
- `addCustomerRelation` - Add customer relation (might be duplicate of current add)
- `delCustomerRelation` - Delete customer relation (might be duplicate of current delete)
- `getCustomerRelation` - Get customer relation (might be duplicate of current get)

### 9. **SaleResource** (Basic implementation)
Missing endpoints:
- `delSale` - Delete sale
- `updateSale` - Update sale

### 10. **TaskResource** (Mostly complete)
Missing endpoints:
- `executeTaskProceeding` - Execute task proceeding

### 11. **TermResource** (Basic implementation)
Missing endpoints:
- `delTerm` - Delete term
- `updateTerm` - Update term

### 12. **UserResource** (Complete for basic operations)
No missing endpoints

## Missing Resource Types (New resources needed)

### 1. **AgentResource**
- `addAgent` - Add agent
- `listAgent` - List agents

### 2. **CategoryResource**
- `addCat` - Add category
- `updateCat` - Update category
- `delCat` - Delete category
- `listCat` - List categories

### 3. **CommissionResource**
- `addCommission` - Add commission
- `updateCommission` - Update commission
- `delCommission` - Delete commission
- `getCommission` - Get commission
- `listCommissions` - List commissions

### 4. **FormResource**
- `addForm` - Add form
- `updateForm` - Update form
- `delForm` - Delete form
- `getForm` - Get form
- `searchForm` - Search forms
- `addFormFile` - Add form file (draft)
- `delFormFile` - Delete form file (draft)
- `loadFormFile` - Load form file (draft)

### 5. **NoticeResource**
- `addNotice` - Add notice
- `updateNotice` - Update notice
- `delNotice` - Delete notice
- `getNotice` - Get notice

### 6. **ProjectResource**
- `getProject` - Get project
- `listProject` - List projects
- `addProjecttype` - Add project type
- `updateProjecttype` - Update project type
- `delProjecttype` - Delete project type

### 7. **TimeResource**
- `getTime` - Get time

### 8. **RemindResource** (ReminderResource exists but might need renaming or extending)
- `addRemind` - Add reminder
- `updateRemind` - Update reminder
- `delRemind` - Delete reminder
- `getRemind` - Get reminder

### 9. **GeneralApiResource** (For miscellaneous endpoints)
- `executeApi` - Execute API
- `uploadFile` - Upload file (generic)
- `getFileList` - Get file list (generic)
- `getSelect` - Get select options

## Summary

### Resources with Missing Methods: 11
1. AccountResource (2 endpoints)
2. ActionResource (1 endpoint)
3. ArticleResource (2 endpoints)
4. CustomerResource (8 endpoints)
5. ObjectResource (7 endpoints)
6. ReceiptResource (8 endpoints)
7. RelationResource (3 endpoints - possibly duplicates)
8. SaleResource (2 endpoints)
9. TaskResource (1 endpoint)
10. TermResource (2 endpoints)
11. ReminderResource (4 endpoints if Remind is separate)

### New Resources Needed: 9
1. AgentResource (2 endpoints)
2. CategoryResource (5 endpoints)
3. CommissionResource (5 endpoints)
4. FormResource (8 endpoints)
5. NoticeResource (4 endpoints)
6. ProjectResource (5 endpoints)
7. TimeResource (1 endpoint)
8. RemindResource (4 endpoints - or extend ReminderResource)
9. GeneralApiResource (4 endpoints)

### Total Missing Endpoints: ~70