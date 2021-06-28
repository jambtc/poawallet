# storage eth-transactions

Put the file `ethstorage.service` to /lib/systemd/system. Then register a service:

```bash
systemctl daemon-reload
systemctl start ethstorage.service
systemctl enable ethstorage.service

```
