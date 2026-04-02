SELECT 'currencies:' as tbl, COUNT(*) as total FROM currencies;
SELECT 'settings:' as tbl, COUNT(*) as total FROM settings;
SELECT id, currency_name, symbol FROM currencies;
SELECT id, company_name, default_currency_id FROM settings;
