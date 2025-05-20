import time
import logging
from binance.client import Client
from binance.enums import *
import numpy as np

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Binance API credentials - replace with your own keys
# For testnet (API de teste) use the following base URL and testnet keys
API_KEY = 'm23XNxFflsWbVt2WzjmeG9cLNFUizgtaZo7zQS1MVzKBE3DkEX9uIw0Rz0r6ww1A'
API_SECRET = 'OVC1WShym9v6ECxzHLcpb9sNAZjBBpfUHII7VFuxuIfJELZZP42dKVSLnGRHDi9k'

# Initialize Binance client for testnet
client = Client(API_KEY, API_SECRET, testnet=True)

# Trading parameters
symbol = 'EURUSDT'
interval = Client.KLINE_INTERVAL_1MINUTE
quantity = 10  # Adjust the quantity to trade
stop_loss_pct = 0.002  # 0.2% stop loss
take_profit_pct = 0.004  # 0.4% take profit

def get_klines(symbol, interval, lookback=100):
    """Fetch historical klines from Binance"""
    klines = client.get_klines(symbol=symbol, interval=interval, limit=lookback)
    closes = [float(kline[4]) for kline in klines]
    highs = [float(kline[2]) for kline in klines]
    lows = [float(kline[3]) for kline in klines]
    return closes, highs, lows

def moving_average(data, period):
    """Calculate simple moving average"""
    if len(data) < period:
        return None
    return np.mean(data[-period:])

def rsi(data, period=14):
    """Calculate Relative Strength Index (RSI)"""
    if len(data) < period + 1:
        return None
    deltas = np.diff(data)
    seed = deltas[:period]
    up = seed[seed >= 0].sum() / period
    down = -seed[seed < 0].sum() / period
    if down == 0:
        return 100
    rs = up / down
    rsi = 100 - (100 / (1 + rs))
    for delta in deltas[period:]:
        if delta > 0:
            upval = delta
            downval = 0
        else:
            upval = 0
            downval = -delta
        up = (up * (period -1) + upval) / period
        down = (down * (period -1) + downval) / period
        if down == 0:
            rsi = 100
        else:
            rs = up / down
            rsi = 100 - (100 / (1 + rs))
    return rsi

def fibonacci_levels(high, low):
    """Calculate Fibonacci retracement levels"""
    diff = high - low
    levels = {
        'level_0': high,
        'level_236': high - 0.236 * diff,
        'level_382': high - 0.382 * diff,
        'level_5': high - 0.5 * diff,
        'level_618': high - 0.618 * diff,
        'level_786': high - 0.786 * diff,
        'level_1': low
    }
    return levels

def place_order(side, quantity, symbol):
    """Place a market order"""
    try:
        order = client.create_order(
            symbol=symbol,
            side=side,
            type=ORDER_TYPE_MARKET,
            quantity=quantity
        )
        logging.info(f"Order placed: {side} {quantity} {symbol}")
        return order
    except Exception as e:
        logging.error(f"Error placing order: {e}")
        return None

def main():
    logging.info("Starting enhanced Binance EUR/USD trading bot")

    position = None  # Track current position: 'LONG' or None
    entry_price = None

    while True:
        try:
            closes, highs, lows = get_klines(symbol, interval)
            if len(closes) < 20:
                logging.info("Not enough data to calculate indicators")
                time.sleep(3)
                continue

            ma_short = moving_average(closes, 5)
            ma_long = moving_average(closes, 20)
            current_rsi = rsi(closes)
            fib_levels = fibonacci_levels(max(highs), min(lows))
            current_price = closes[-1]

            logging.info(f"MA5: {ma_short:.5f}, MA20: {ma_long:.5f}, RSI: {current_rsi:.2f}, Price: {current_price:.5f}")

            if ma_short is None or ma_long is None or current_rsi is None:
                time.sleep(3)
                continue

            # Entry conditions: MA crossover + RSI confirmation + price near Fibonacci support/resistance
            buy_signal = (ma_short > ma_long) and (current_rsi < 70) and (current_price > fib_levels['level_618'])
            sell_signal = (ma_short < ma_long) and (current_rsi > 30) and (current_price < fib_levels['level_382'])

            if position != 'LONG' and buy_signal:
                order = place_order(SIDE_BUY, quantity, symbol)
                if order:
                    position = 'LONG'
                    entry_price = current_price
                    logging.info(f"Entered LONG position at {entry_price:.5f}")

            elif position == 'LONG':
                # Check stop loss and take profit
                if current_price <= entry_price * (1 - stop_loss_pct):
                    order = place_order(SIDE_SELL, quantity, symbol)
                    if order:
                        logging.info(f"Stop loss triggered at {current_price:.5f}")
                        position = None
                        entry_price = None
                elif current_price >= entry_price * (1 + take_profit_pct):
                    order = place_order(SIDE_SELL, quantity, symbol)
                    if order:
                        logging.info(f"Take profit triggered at {current_price:.5f}")
                        position = None
                        entry_price = None
                elif sell_signal:
                    order = place_order(SIDE_SELL, quantity, symbol)
                    if order:
                        logging.info(f"Sell signal triggered at {current_price:.5f}")
                        position = None
                        entry_price = None

            time.sleep(3)  # Wait for next candle

        except Exception as e:
            logging.error(f"Error in main loop: {e}")
            time.sleep(3)

if __name__ == "__main__":
    main()
