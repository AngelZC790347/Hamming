#define CLIENT 4
const char ledsPins [] = {5,6,7,8,9,10,11,12};
int option;
void setup() {
    Serial.begin(9600);     
    for (int i = 0; i < 8; ++i)
     {
         pinMode(ledsPins[i], OUTPUT);
     } 
     pinMode(CLIENT,OUTPUT);
}
void loop() {
    if (Serial.available()>=1) 
    {
        option = Serial.parseInt();
        Serial.println(option);
    }
}