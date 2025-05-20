import requests
import pandas as pd
import time

def download_binance_klines(symbol, interval, start_str, end_str=None, limit=1000):
    """
    Download historical klines (candlestick) data from Binance API.
    symbol: trading pair symbol, e.g. 'EURUSDT'
    interval: interval string, e.g. '1m', '1h', '1d'
    start_str: start date string in milliseconds or date string e.g. '1 Jan, 2020'
    end_str: end date string in milliseconds or date string
    limit: max number of data points per request (max 1000)
    Returns a list of klines.
    """
    url = "https://api.binance.com/api/v3/klines"
    params = {
        'symbol': symbol,
        'interval': interval,
        'startTime': None,
        'endTime': None,
        'limit': limit
    }

    # Convert start_str and end_str to milliseconds timestamp if needed
    import dateparser
    start_ts = int(dateparser.parse(start_str).timestamp() * 1000)
    params['startTime'] = start_ts
    if end_str:
        end_ts = int(dateparser.parse(end_str).timestamp() * 1000)
        params['endTime'] = end_ts

    all_klines = []
    while True:
        response = requests.get(url, params=params)
        data = response.json()
        if not data:
            break
        all_klines.extend(data)
        if len(data) < limit:
            break
        last_open_time = data[-1][0]
        params['startTime'] = last_open_time + 1
        time.sleep(0.5)  # to avoid rate limits

    return all_klines

def klines_to_dataframe(klines):
    """
    Convert klines list to pandas DataFrame with relevant columns.
    """
    df = pd.DataFrame(klines, columns=[
        'open_time', 'open', 'high', 'low', 'close', 'volume',
        'close_time', 'quote_asset_volume', 'number_of_trades',
        'taker_buy_base_asset_volume', 'taker_buy_quote_asset_volume', 'ignore'
    ])
    df['open'] = df['open'].astype(float)
    df['high'] = df['high'].astype(float)
    df['low'] = df['low'].astype(float)
    df['close'] = df['close'].astype(float)
    df['volume'] = df['volume'].astype(float)
    return df[['open', 'high', 'low', 'close', 'volume']]

if __name__ == "__main__":
    import sys
    if len(sys.argv) < 4:
        print("Usage: python download_historical_data.py SYMBOL INTERVAL START_DATE [END_DATE]")
        print("Example: python download_historical_data.py EURUSDT 1m '1 Jan, 2023' '1 Feb, 2023'")
        sys.exit(1)

    symbol = sys.argv[1]
    interval = sys.argv[2]
    start_date = sys.argv[3]
    end_date = sys.argv[4] if len(sys.argv) > 4 else None

    klines = download_binance_klines(symbol, interval, start_date, end_date)
    df = klines_to_dataframe(klines)
    filename = f"{symbol}_{interval}_{start_date.replace(' ', '_')}.csv"
    df.to_csv(filename, index=False)
    print(f"Historical data saved to {filename}")
