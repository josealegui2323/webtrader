import os
import time
import subprocess

# Caminho da pasta onde o arquivo de sinal será criado
signal_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'bot')
signal_file = os.path.join(signal_dir, 'activate_bot.signal')

# Caminho do script do bot Python
bot_script = os.path.join(signal_dir, 'binance_eurusd_bot.py')

def run_bot():
    try:
        result = subprocess.run(['python', bot_script], capture_output=True, text=True)
        print(f"Bot output: {result.stdout}")
        if result.returncode != 0:
            print(f"Bot error: {result.stderr}")
    except Exception as e:
        print(f"Erro ao executar o bot: {e}")

def main():
    print("Serviço do bot iniciado. Monitorando sinal...")
    while True:
        if os.path.exists(signal_file):
            print("Sinal detectado. Executando bot...")
            run_bot()
            try:
                os.remove(signal_file)
                print("Sinal removido após execução.")
            except Exception as e:
                print(f"Erro ao remover sinal: {e}")
        time.sleep(5)  # Verifica a cada 5 segundos

if __name__ == "__main__":
    main()
