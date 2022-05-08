docker exec -t postgres pg_dumpall -c -U admin > dump_`date +%d-%m-%Y"_"%H_%M_%S`.sql
