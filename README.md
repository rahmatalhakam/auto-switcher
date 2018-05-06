# auto-switcher
Auto Switch Mining Program for Nanopool Server

This program is used to mine the most profit coins on nanopool.org server. It will calculate the most profit coins, then will execute the mining program. Windows x64 bit only.

# How to use
1. Install php on your computer.
2. Add php folder to the environment variables on windows
3. Edit data in data.csv and wallet.csv
4. Run algoOptimizer.bat
5. enjoy

# How to set the data.csv and wallet.csv
>Note: Don't change the orientation of data.csv and wallet.csv.

Please set and write the configuration for
1. data.csv
    - Worker Name,[worker_name],
    - Email,[your_email],
    - PLN,[electricity_cost],[tax_fee]
    - Ethash,[hash/s],[pemakaian_listrik]
    - Cryptonight,[hash/s],[pemakaian_listrik]
    - Equihash,[hash/s],[pemakaian_listrik]
2. wallet.csv
    - Wallet ETH,[Wallet_ETH]
    - Wallet ETC,[Wallet ETC]
    - Wallet XMR,[Wallet XMR]
    - Payment ID XMR (optional),[Payment_ID_XMR]
    - Wallet ETN,[Wallet_ETN]
    - Payment ID ETN (optional),[Payment_ID_ETN]
    - Wallet ZEC,[Wallet_ZEC]