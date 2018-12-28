/// <reference path='./Shape.ts'/>

abstract class OnePointShape extends Shape
{
    StartPoint : Point;
    public constructor(p : Point)
    {
        super();
        this.StartPoint = p;
    }
}