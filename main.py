import serial
import sys

s = serial.Serial("/dev/cu.usbserial-1410")

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Error usage only one argument")
        sys.exit(1)
    data = list(sys.argv[1])
    data = list(map(lambda x: int(x), data))
    for x in data:
        s.write(str(x).encode())
        s.write('\n'.encode())
