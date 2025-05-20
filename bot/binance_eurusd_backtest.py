import numpy as np
import pandas as pd

def moving_average(data, period):
    if len(data) < period:
        return None
    return np.mean(data[-period:])

def rsi(data, period=14):
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

def backtest_strategy(df, quantity=10, stop_loss_pct=0.002, take_profit_pct=0.004):
    """
    Backtest the trading strategy on historical data.
    df: pandas DataFrame with columns ['close', 'high', 'low']
    """
    position = None
    entry_price = 0
    balance = 0
    trades = 0
    wins = 0
    losses = 0

    for i in range(20, len(df)):
        closes = df['close'].iloc[:i].values
        highs = df['high'].iloc[:i].values
        lows = df['low'].iloc[:i].values

        ma_short = np.mean(closes[-5:])
        ma_long = np.mean(closes[-20:])
        current_rsi = rsi(closes)
        fib_levels = fibonacci_levels(max(highs), min(lows))
        current_price = closes[-1]

        buy_signal = (ma_short > ma_long) and (current_rsi < 70) and (current_price > fib_levels['level_618'])
        sell_signal = (ma_short < ma_long) and (current_rsi > 30) and (current_price < fib_levels['level_382'])

        if position != 'LONG' and buy_signal:
            position = 'LONG'
            entry_price = current_price
            trades += 1

        elif position == 'LONG':
            if current_price <= entry_price * (1 - stop_loss_pct):
                # Stop loss triggered
                balance += (current_price - entry_price) * quantity
                position = None
                losses += 1
            elif current_price >= entry_price * (1 + take_profit_pct):
                # Take profit triggered
                balance += (current_price - entry_price) * quantity
                position = None
                wins += 1
            elif sell_signal:
                # Sell signal triggered
                balance += (current_price - entry_price) * quantity
                position = None
                if current_price > entry_price:
                    wins += 1
                else:
                    losses += 1

    return {
        'total_trades': trades,
        'wins': wins,
        'losses': losses,
        'win_rate': wins / trades if trades > 0 else 0,
        'net_profit': balance
    }

if __name__ == "__main__":
    # Example usage with CSV file containing historical data
    # CSV should have columns: open, high, low, close, volume, etc.
    import sys
    if len(sys.argv) < 2:
        print("Usage: python binance_eurusd_backtest.py path_to_csv")
        sys.exit(1)

    csv_path = sys.argv[1]
    df = pd.read_csv(csv_path)
    if not {'close', 'high', 'low'}.issubset(df.columns):
        print("CSV file must contain 'close', 'high', and 'low' columns")
        sys.exit(1)

    results = backtest_strategy(df)
    print("Backtest Results:")
    print(f"Total Trades: {results['total_trades']}")
    print(f"Wins: {results['wins']}")
    print(f"Losses: {results['losses']}")
    print(f"Win Rate: {results['win_rate']*100:.2f}%")
    print(f"Net Profit: {results['net_profit']:.2f}")
