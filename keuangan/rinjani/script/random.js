class Rnd
{
    static Integer(min = 0, max = 10)
    {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    static String(length = 10)
    {
        return Math.random().toString(36).substring(2, 2 + length);
    }
}
