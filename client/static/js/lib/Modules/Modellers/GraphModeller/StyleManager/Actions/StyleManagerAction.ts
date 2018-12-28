/// <reference path='./index.ts'/>
/// <reference path='../../../../../Action/index.ts'/>

abstract class StyleManagerAction
{
    protected Green = "#1fb20899";
    protected Red   = "#ff000077";
    protected Orange = "#FC8D2ACC";

    public abstract Invoke(context : CanvasRenderingContext2D);
}