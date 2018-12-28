/// <reference path='./index.ts'/>

abstract class HTMLInputGenerator<E extends HTMLElement, V> extends HTMLGenerator<E>
{
    public Value : V;
    public abstract UpdateValue(newValue : V);
}