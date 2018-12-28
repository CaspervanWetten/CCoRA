/// <reference path='./TwoPointShape.ts'/>

abstract class ThreePointShape extends TwoPointShape
{
    ThirdPoint : Point;
    public constructor(a : Point, b : Point, c : Point)
    {
        super(a, b);
        this.ThirdPoint = c;
    }
}