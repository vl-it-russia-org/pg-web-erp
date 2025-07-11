# pg-web-erp
Web ERP system based on PHP + PDO + PostgreSQL stack technology

How to test functionality:

1) Upload all files from GitHub
2) Setup on Your server PHP (with PDO) and PostgreSQL
3) Create database web_erp on Your PostreSQL sever ( for $bash: createdb -U web_erp -h localhost web_erp)
4) Upload database dump into created database in p.3.
   4.1) Unzip file web_erp_2025-07-11.zip into new folder: for example, /var/var/Files. You will get file /var/var/Files/web_erp_2025-07-11.backup
   4.2) Come to /var/var/Files: cd /var/var/Files
   4.3) Restore data to web_erp database: run in $bash: pg_restore -U web_erp -h localhost -d web_erp -v web_erp_2025-07-11.backup

   
