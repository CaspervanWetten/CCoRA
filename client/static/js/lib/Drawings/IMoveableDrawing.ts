/// <reference path='../Utils/Datastructures/Point/Point.ts'/>

interface IMoveableDrawing
{
    MoveTo(point : Point)   : void;
    GetPosition()           : Point;
}