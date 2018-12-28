/// <reference path='../Datastructures/Point/Point.ts'/>

interface Math {
    radToDeg(radians : number) : number;
    degToRad(degrees : number) : number;
    CalcHorizontalDistance(a : Point, b : Point) : number;
    CalcVerticalDistance(a : Point, b : Point) : number;
    calcAngle(a : Point, b : Point) : number;
}

Math.radToDeg = function(radians : number)
{
    return radians / (Math.PI / 180) % 360;
}

Math.degToRad = function(degrees : number)
{
    return degrees * (Math.PI / 180) % (2 * Math.PI);
}

Math.CalcHorizontalDistance = function(a : Point, b : Point)
{
    let res = Math.abs(a.X - b.X);
    return res;
}

Math.CalcVerticalDistance = function(a : Point, b : Point)
{
    let res = Math.abs(a.Y - b.Y);
    return res;
}

Math.calcAngle = function(from : Point, to : Point)
{
    let dx = Math.CalcHorizontalDistance(from, to);
    let dy = Math.CalcVerticalDistance(from, to);

    if(dx == 0) {
        let k = 0.5 * Math.PI;
        if(from.Y < to.Y){
            k += Math.PI;
        }
        return k;
    }

    let theta = Math.atan(dy/dx);

    if (from.Y < to.Y) // top to bottom
        theta *= -1;

    if (from.X >= to.X) // right to left
        theta = Math.PI - theta;

    if (to.X > from.X && from.Y < to.Y)
        theta = (2 * Math.PI) - Math.abs(theta);

    return theta;
}