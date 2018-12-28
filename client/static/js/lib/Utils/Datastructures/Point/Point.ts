class Point
{
    public X : number;
    public Y : number;

    public constructor(x : number, y : number)
    {
        this.X = x;
        this.Y = y;
    }

    public ToString()
    {
        let s = "(";
        s += this.X.toString();
        s += ", ";
        s += this.Y.toString();
        s += ")";

        return s;
    }
}